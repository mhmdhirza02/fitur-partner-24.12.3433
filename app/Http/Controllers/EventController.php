<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    function index(){
    
    }

    public function show(\App\Models\Event $event)
{
    // Mengambil daftar kategori untuk keperluan menu footer
    $categories = \App\Models\Category::all();
    $event->load('ticketTiers');
    
    // Me-render view dengan membawa data kategori dan data spesifik acara tersebut
    return view('event-detail', compact('categories', 'event'));
}


    function checkout(){
        return view('checkout');
    }

    function ticket($order_id){
        $transaction = \App\Models\Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('ticket', compact('transaction'));
    }

    public function myTickets(Request $request)
    {
        $query = \App\Models\Transaction::with(['event', 'review'])->latest();

        if (auth()->check()) {
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            } else {
                $query->where('customer_email', auth()->user()->email);
            }
        } else {
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%");
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $transactions = $query->get();
        return view('my-tickets', compact('transactions'));
    }
}
