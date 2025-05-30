<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BranchController extends Controller
{
    protected $api;

    public function __construct()
    {   
        $this->api = 'http://localhost:7001';
    }


    public function index()
    {
        try {
            $response = Http::get($this->api . '/api/Branch');
            
            if ($response->successful()) {
                $data = $response->json();
                return view('branch', compact('data'));
            } else {
                return view('branch', [
                    'data' => [],
                    'error' => 'Gagal mengambil data dari API: ' . $response->status()
                ]);
            }
        } catch (\Exception $e) {
            return view('branch', [
                'data' => [],
                'error' => 'Error koneksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created branch
     */
    public function store(Request $request)
    {
        $request->validate([
            'branchName' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ], [
            'branchName.required' => 'Nama branch wajib diisi',
            'branchName.max' => 'Nama branch maksimal 255 karakter',
            'address.required' => 'Alamat wajib diisi',
            'address.max' => 'Alamat maksimal 500 karakter',
        ]);

        try {
            $response = Http::post($this->api . '/api/Branch', [
                'branchName' => $request->branchName,
                'address' => $request->address,
            ]);

            if ($response->successful()) {
                return redirect()->route('branch.index')
                    ->with('success', 'Branch "' . $request->branchName . '" berhasil ditambahkan!');
            } else {
                $errorMessage = 'Gagal menambahkan branch';
                
                if ($response->status() == 400) {
                    $errorMessage = 'Data yang dikirim tidak valid';
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

    /**
     * Update the specified branch
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'branchName' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ], [
            'branchName.required' => 'Nama branch wajib diisi',
            'branchName.max' => 'Nama branch maksimal 255 karakter',
            'address.required' => 'Alamat wajib diisi',
            'address.max' => 'Alamat maksimal 500 karakter',
        ]);

        try {
            // PUT data ke API external
            $response = Http::put($this->api . '/api/Branch/' . $id, [
                'branchName' => $request->branchName,
                'address' => $request->address,
            ]);

            // Cek response dari API
            if ($response->successful()) {
                return redirect()->route('branch.index')
                    ->with('success', 'Branch "' . $request->branchName . '" berhasil diupdate!');
            } else {
                // Handle error response dari API
                $errorMessage = 'Gagal mengupdate branch';
                
                if ($response->status() == 400) {
                    $errorMessage = 'Data yang dikirim tidak valid';
                } elseif ($response->status() == 404) {
                    $errorMessage = 'Branch tidak ditemukan';
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

    /**
     * Remove the specified branch
     */
    public function destroy(Request $request)
{
    try {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->send('DELETE', $this->api . '/api/Branch', [
            'json' => ['id' => (int) $request->id],
        ]);

        if ($response->successful()) {
            return redirect()->route('branch.index')->with('success', 'Branch berhasil dihapus!');
        } else {
            $errorMessage = 'Gagal menghapus branch';

            if ($response->status() == 404) {
                $errorMessage = 'Branch tidak ditemukan atau sudah dihapus';
            } elseif ($response->status() == 400) {
                $errorMessage = 'Branch tidak dapat dihapus karena masih memiliki relasi data';
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

    /**
     * Get single branch data (optional - for AJAX requests)
     */
    public function show($id)
    {
        try {
            $response = Http::get($this->api . '/api/Branch/' . $id);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Branch tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}