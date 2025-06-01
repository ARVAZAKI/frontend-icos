<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
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
            $selectedBranch = null;

            if ($request->has('branch_id') && !empty($request->branch_id)) {
                $categoryResponse = Http::get($this->api . '/api/Category/branch/' . $request->branch_id);
                $categories = $categoryResponse->successful() ? $categoryResponse->json() : [];
                $selectedBranch = $request->branch_id;
            }

            return view('category', compact('branches', 'categories', 'selectedBranch'));

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return view('category', [
                'branches' => [],
                'categories' => [],
                'selectedBranch' => null
            ])->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.');
        } catch (\Exception $e) {
            return view('category', [
                'branches' => [],
                'categories' => [],
                'selectedBranch' => null
            ])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoryName' => 'required|string|max:255',
            'branch_id' => 'required|integer'
        ], [
            'categoryName.required' => 'Nama kategori wajib diisi',
            'categoryName.max' => 'Nama kategori maksimal 255 karakter',
            'branch_id.required' => 'Cabang wajib dipilih',
            'branch_id.integer' => 'Cabang tidak valid',
        ]);
            
            if (!$this->token) {
                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
        try {
            $response = Http::withToken($this->token)->post($this->api . '/api/Category', [
                'categoryName' => $request->categoryName,
                'branchId' => $request->branch_id
            ]);

            if ($response->successful()) {
                return redirect()->route('category.index', ['branch_id' => $request->branch_id])
                    ->with('success', 'Kategori "' . $request->categoryName . '" berhasil ditambahkan!');
            } else {
                $errorMessage = 'Gagal menambahkan kategori';
                
                if ($response->status() == 400) {
                    $errorMessage = 'Data yang dikirim tidak valid';
                } elseif ($response->status() == 404) {
                    $errorMessage = 'Cabang tidak ditemukan';
                } elseif ($response->status() == 500) {
                    $errorMessage = 'Terjadi kesalahan pada server API';
                }
                
                return back()
                    ->with('error', $errorMessage . ' (HTTP ' . $response->status() . ')')
                    ->withInput();
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.')
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'categoryName' => 'required|string|max:255',
            'branch_id' => 'required|integer'
        ], [
            'id.required' => 'ID kategori wajib diisi',
            'id.integer' => 'ID kategori tidak valid',
            'categoryName.required' => 'Nama kategori wajib diisi',
            'categoryName.max' => 'Nama kategori maksimal 255 karakter',
            'branch_id.required' => 'Cabang wajib dipilih',
            'branch_id.integer' => 'Cabang tidak valid',
        ]);

        try {
            $response = Http::withToken($this->token)->put($this->api . '/api/Category', [
                'id' => $request->id,
                'categoryName' => $request->categoryName,
                'branchId' => $request->branch_id
            ]);

            if ($response->successful()) {
                return redirect()->route('category.index', ['branch_id' => $request->branch_id])
                    ->with('success', 'Kategori "' . $request->categoryName . '" berhasil diupdate!');
            } else {
                $errorMessage = 'Gagal mengupdate kategori';
                
                if ($response->status() == 400) {
                    $errorMessage = 'Data yang dikirim tidak valid';
                } elseif ($response->status() == 404) {
                    $errorMessage = 'Kategori tidak ditemukan';
                } elseif ($response->status() == 500) {
                    $errorMessage = 'Terjadi kesalahan pada server API';
                }
                
                return back()
                    ->with('error', $errorMessage . ' (HTTP ' . $response->status() . ')')
                    ->withInput();
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->with('error', 'Tidak dapat terhubung ke API server. Pastikan API server berjalan.')
                ->withInput();
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ], [
            'id.required' => 'ID kategori wajib diisi',
            'id.integer' => 'ID kategori tidak valid',
        ]);

        try {
             $response = Http::withToken($this->token)->delete($this->api . '/api/Category', [
            'id' => (int) $request->id
        ]);

            if ($response->successful()) {
                return back()->with('success', 'Kategori berhasil dihapus!');
            } else {
                $errorMessage = 'Gagal menghapus kategori';

                if ($response->status() == 404) {
                    $errorMessage = 'Kategori tidak ditemukan atau sudah dihapus';
                } elseif ($response->status() == 400) {
                    $errorMessage = 'Kategori tidak dapat dihapus karena masih memiliki relasi data';
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

    public function show($id)
    {
        try {
            $response = Http::get($this->api . '/api/Category/' . $id);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Kategori tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}