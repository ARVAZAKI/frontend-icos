<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    protected $api;

    public function __construct(){
        $this->api = Config::get('app.apiurl');
    }
    
    public function index(Request $request){
    $transactions = Http::get($this->api . '/api/Transaction');
    $products = Http::get($this->api . '/api/Product');
    $branches = Http::get($this->api . '/api/Branch');
    
    $selectedBranch = $request->get('branch_id');
    
    $filteredTransactions = [];
    if ($selectedBranch && $transactions->successful()) {
        $allTransactions = $transactions->json();
        $filteredTransactions = array_filter($allTransactions, function($transaction) use ($selectedBranch) {
            return $transaction['branchId'] == $selectedBranch;
        });
    }
    
    return view('order', compact('filteredTransactions', 'products', 'branches', 'selectedBranch'));
    }
}
