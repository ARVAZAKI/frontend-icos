<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
     protected $api;
    protected $token;
    public function __construct()
    {   
        $this->api = Config::get('app.apiurl');
        $this->token = Session::get('api_token');
    }
    public function index(Request $request)
    {
        try {
            $branchResponse = Http::get($this->api . '/api/Branch');
            $branches = $branchResponse->successful() ? $branchResponse->json() : [];

            $categories = [];
            $products = [];
            $selectedBranch = null;

            if ($request->has('branch_id') && !empty($request->branch_id)) {
                $productResponse = Http::get($this->api . '/api/Product/branch/' . $request->branch_id);
                $products = $productResponse->successful() ? $productResponse->json() : [];
                
                $categoryResponse = Http::get($this->api . '/api/Category/branch/' . $request->branch_id);
                $categories = $categoryResponse->successful() ? $categoryResponse->json() : [];
                
                $selectedBranch = $request->branch_id;
            }

            return view('product', compact('branches', 'products', 'categories', 'selectedBranch'));

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return view('product', [
                'branches' => [],
                'products' => [],
                'categories' => [],
                'selectedBranch' => null
            ])->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.');
        } catch (\Exception $e) {
            return view('product', [
                'branches' => [],
                'products' => [],
                'categories' => [],
                'selectedBranch' => null
            ])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
{
    try {
        Log::info('Form Data Received:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoryId' => 'required|integer',
            'branch_id' => 'required|integer',
            'description' => 'required',
            'ImageFile' => 'required|image|mimes:jpeg,png,jpg'
        ]);

        $data = [
            'Name' => trim($request->name),
            'Price' => (int) $request->price,
            'Stock' => (int) $request->stock,
            'Description' => trim($request->description),
            'CategoryId' => (int) $request->categoryId,
            'BranchId' => (int) $request->branch_id,
            'IsActive' => 'true'
        ];

        Log::info('Data to be sent to API:', $data);

        if ($request->hasFile('ImageFile') && $request->file('ImageFile')->isValid()) {
            $file = $request->file('ImageFile');
            Log::info('File info:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);
            
            $data['ImageFile'] = fopen($file->getRealPath(), 'r');
            
            $response = Http::withToken($this->token)
                ->asMultipart()
                ->timeout(30)
                ->post($this->api . '/api/Product', $data);
                
            // Tutup file handle
            if (is_resource($data['ImageFile'])) {
                fclose($data['ImageFile']);
            }
        } else {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->timeout(30)
                ->post($this->api . '/api/Product', $data);
        }

        // Debug: Log response
        Log::info('API Response:', [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body()
        ]);

        if ($response->successful()) {
            return redirect()->route('product.index', ['branch_id' => $request->branch_id])
                ->with('success', 'Produk "' . $request->name . '" berhasil ditambahkan!');
        } else {
            $errorMessage = 'Gagal menambahkan produk';
            $responseBody = $response->json();
            
            if (is_array($responseBody)) {
                if (isset($responseBody['message'])) {
                    $errorMessage = $responseBody['message'];
                } elseif (isset($responseBody['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($responseBody['errors']);
                } elseif (isset($responseBody['error'])) {
                    $errorMessage = $responseBody['error'];
                }
            }
            
            // Log error untuk debugging
            Log::error('API Error Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'sent_data' => $data
            ]);
            
            return back()
                ->with('error', $errorMessage . ' (HTTP ' . $response->status() . ')')
                ->withInput();
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('Connection Exception:', ['message' => $e->getMessage()]);
        return back()
            ->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.')
            ->withInput();
    } catch (\Exception $e) {
        Log::error('Product Store Exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

    public function show($id)
    {
        try {
            $response = Http::get($this->api . '/api/Product/' . $id);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Produk tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
             $response = Http::withToken($this->token)->delete($this->api . '/api/Product/'. $id);

            if ($response->successful()) {
                return back()->with('success', 'Kategori berhasil dihapus!');
            } else {
                $errorMessage = 'Gagal menghapus Produk';

                if ($response->status() == 404) {
                    $errorMessage = 'Produk tidak ditemukan atau sudah dihapus';
                } elseif ($response->status() == 400) {
                    $errorMessage = 'Produk tidak dapat dihapus karena masih memiliki relasi data';
                } elseif ($response->status() == 500) {
                    $errorMessage = 'Terjadi kesalahan pada server API';
                }

                return back()->with('error', $errorMessage . ' (HTTP ' . $response->status() . ')');
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id){
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoryId' => 'required|integer',
            'branch_id' => 'required|integer',
            'description' => 'nullable|string',
            'ImageFile' => 'nullable|image|mimes:jpeg,png,jpg'
        ]);

        $data = [
            'Name' => trim($request->name),
            'Price' => (int) $request->price,
            'Stock' => (int) $request->stock,
            'Description' => trim($request->description),
            'CategoryId' => (int) $request->categoryId,
            'BranchId' => (int) $request->branch_id,
            'IsActive' => 'true'
        ];

        if ($request->hasFile('ImageFile') && $request->file('ImageFile')->isValid()) {
            $file = $request->file('ImageFile');
            Log::info('Uploading new image:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);
            
            $data['ImageFile'] = fopen($file->getRealPath(), 'r');
        } else {
            $data['ImageFile'] = ''; 
        }
        
        $response = Http::withToken($this->token)
            ->asMultipart()
            ->timeout(30)
            ->put($this->api . '/api/Product/' . $id, $data);
            
        if (isset($data['ImageFile']) && is_resource($data['ImageFile'])) {
            fclose($data['ImageFile']);
        }

        if ($response->successful()) {
            return back()->with('success', 'Produk berhasil diupdate!');
        } else {
            $errorMessage = 'Gagal mengupdate Produk';

            if ($response->status() == 400) {
                $errorMessage = 'Data yang dikirim tidak valid';
                Log::error('API Error 400:', ['response' => $response->body()]);
            } elseif ($response->status() == 500) {
                $errorMessage = 'Terjadi kesalahan pada server API';
                Log::error('API Error 500:', ['response' => $response->body()]);
            }

            return back()->with('error', $errorMessage . ' (HTTP ' . $response->status() . ')');
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('Connection Error:', ['message' => $e->getMessage()]);
        return back()->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.');
    } catch (\Exception $e) {
        Log::error('General Error:', ['message' => $e->getMessage()]);
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}