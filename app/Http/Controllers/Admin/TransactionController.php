<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $query = Transaction::with('event');
        
        if (auth()->user()->role === 'partner') {
            $query->whereHas('event', function ($q) {
                $q->where('partner_id', auth()->user()->partner_id);
            });
        }

        $transactions = $query->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }
}