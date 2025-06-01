<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
     protected $api;

    public function __construct()
    {   
        $this->api = Config::get('app.apiurl');
    }

     public function index()
    {
        if (Session::has('api_token')) {
            return redirect()->route('branch.index');
        }
        
        return view('login');
    }
    public function showtoken(){
        return Session::get('api_token');
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        try {
            $response = Http::post($this->api . '/api/Auth/login', [
                'username' => $request->username,
                'password' => $request->password
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Session::put('api_token', $data['token']);
                Session::put('user_id', $data['id']);
                Session::put('username', $data['username']);
                Session::put('email', $data['email']);
                Session::put('role', $data['role']);

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'Login berhasil! Selamat datang, ' . $data['username']);
                    
            } else {
                $errorMessage = 'Login gagal';
                
                if ($response->status() == 401) {
                    $errorMessage = 'Username atau password salah';
                } elseif ($response->status() == 500) {
                    $errorMessage = 'Terjadi kesalahan pada server';
                }
                
                return back()
                    ->with('error', $errorMessage)
                    ->withInput($request->only('username'));
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()
                ->with('error', 'Tidak dapat terhubung ke server. Silakan coba lagi.')
                ->withInput($request->only('username'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput($request->only('username'));
        }
    }

    public function logout()
    {
        Session::forget(['api_token', 'user_id', 'username', 'email', 'role']);
        
        return redirect()->route('login')
            ->with('success', 'Logout berhasil');
    }
}
