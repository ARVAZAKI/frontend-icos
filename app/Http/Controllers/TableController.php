<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class TableController extends Controller
{
    protected $api;

    public function __construct(){
        $this->api = Config::get('app.apiurl');
    }
    public function index(Request $request){
    $tables = Http::get($this->api . '/api/Table');
    $branches = Http::get($this->api . '/api/Branch');
    $tableReservation = Http::get($this->api . '/api/TableReservation');
    $selectedBranch = $request->get('branch_id');
    $filteredTables = [];
    
    if ($selectedBranch && $tables->successful() && $tables->json('data')) {
        $filteredTables = collect($tables->json('data'))->filter(function ($table) use ($selectedBranch) {
            return $table['branchId'] == $selectedBranch;
        })->values()->all();
    }
    
    return view('table', compact('tables', 'branches', 'selectedBranch', 'filteredTables', 'tableReservation'));
    }

    public function store(Request $request){
    $request->validate([
        'tableNumber' => 'required|string|max:255',
        'capacity' => 'required|numeric|min:1',
        'branchId' => 'required|integer',
        'description' => 'required|string',
    ]);

    $data = [
        'tableNumber' => $request->input('tableNumber'),
        'capacity' => $request->input('capacity'),
        'branchId' => $request->input('branchId'),
        'description' => $request->input('description'),
    ];        
    
    try {
        $response = Http::post($this->api . '/api/Table', $data);
        
        if ($response->successful()) {
            return redirect()->route('table.index', ['branch_id' => $request->branchId])
                           ->with('success', 'Meja berhasil ditambahkan!');
        } else {
            return redirect()->back()
                           ->with('error', 'Gagal menambahkan meja. Silakan coba lagi.')
                           ->withInput();
        }
    } catch (\Exception $e) {
        return redirect()->back()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                       ->withInput();
    }
}

public function update(Request $request, $id)
{
    $request->validate([
        'tableNumber' => 'required|string|max:255',
        'capacity' => 'required|numeric|min:1',
        'branchId' => 'required|integer',
        'description' => 'required|string',
    ]);

    $data = [
        'tableNumber' => $request->input('tableNumber'),
        'capacity' => $request->input('capacity'),
        'branchId' => $request->input('branchId'),
        'description' => $request->input('description'),
    ];

    try {
        $response = Http::put($this->api . '/api/Table/' . $id, $data);
        
        if ($response->successful()) {
            return redirect()->route('table.index', ['branch_id' => $request->branchId])
                           ->with('success', 'Meja berhasil diperbarui!');
        } else {
            return redirect()->back()
                           ->with('error', 'Gagal memperbarui meja. Silakan coba lagi.')
                           ->withInput();
        }
    } catch (\Exception $e) {
        return redirect()->back()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                       ->withInput();
    }
}

public function destroy($id)
{
    try {
        $response = Http::delete($this->api . '/api/Table/' . $id);
        
        if ($response->successful()) {
            return redirect()->back()
                           ->with('success', 'Meja berhasil dihapus!');
        } else {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus meja. Silakan coba lagi.');
        }
    } catch (\Exception $e) {
        return redirect()->back()
                       ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
