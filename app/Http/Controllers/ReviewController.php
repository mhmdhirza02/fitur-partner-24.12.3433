<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->with('event', 'review')->firstOrFail();

        // Cek apakah sudah di-review
        if ($transaction->review) {
            return redirect()->route('home')->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        // Cek apakah event sudah lewat
        if (Carbon::now()->lt(Carbon::parse($transaction->event->date))) {
            return redirect()->route('home')->with('error', 'Ulasan baru bisa diberikan setelah acara selesai.');
        }

        return view('reviews.create', compact('transaction'));
    }

    public function store(Request $request, $order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->with('event', 'review')->firstOrFail();

        if ($transaction->review) {
            return redirect()->route('home')->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        if (Carbon::now()->lt(Carbon::parse($transaction->event->date))) {
            return redirect()->route('home')->with('error', 'Ulasan baru bisa diberikan setelah acara selesai.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'transaction_id' => $transaction->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('home')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
