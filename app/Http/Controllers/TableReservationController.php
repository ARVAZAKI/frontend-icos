<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TableReservationController extends Controller
{
    protected $api;

    public function __construct(){
        $this->api = Config::get('app.apiurl');
    }

    public function index(Request $request){
    $branch = Http::get($this->api . '/api/Branch');
    $table = Http::get($this->api . '/api/Table');
    $tableReservation = Http::get($this->api . '/api/TableReservation');
    
    $selectedBranch = $request->get('branch_id');
    
    $filteredReservations = [];
    if ($selectedBranch && $tableReservation->successful()) {
        $allReservations = $tableReservation->json()['data']; // Ada wrapper 'data'
        $filteredReservations = array_filter($allReservations, function($reservation) use ($selectedBranch) {
            return $reservation['branchId'] == $selectedBranch;
        });
    }
    
    return view('table-reservation', compact('filteredReservations', 'table', 'branch', 'selectedBranch'));
    }
        public function confirm(Request $request, $id)
    {
        try {
            $response = Http::post($this->api . '/api/TableReservation/' . $id . '/confirm');
            
            if ($response->successful()) {
                return redirect()->back()->with('success', 'Reservasi berhasil dikonfirmasi');
            } else {
                return redirect()->back()->with('error', 'Gagal mengkonfirmasi reservasi');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function checkin(Request $request, $id)
    {
        try {
            $response = Http::post($this->api . '/api/TableReservation/' . $id . '/checkin');
            
            if ($response->successful()) {
                return redirect()->back()->with('success', 'Check-in berhasil dilakukan');
            } else {
                return redirect()->back()->with('error', 'Gagal melakukan check-in');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, $id)
    {
        try {
            $response = Http::post($this->api . '/api/TableReservation/' . $id . '/complete');
            
            if ($response->successful()) {
                return redirect()->back()->with('success', 'Reservasi berhasil diselesaikan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyelesaikan reservasi');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $response = Http::post($this->api . '/api/TableReservation/' . $id . '/cancel');
            
            if ($response->successful()) {
                return redirect()->back()->with('success', 'Reservasi berhasil dibatalkan');
            } else {
                return redirect()->back()->with('error', 'Gagal membatalkan reservasi');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
        
}
