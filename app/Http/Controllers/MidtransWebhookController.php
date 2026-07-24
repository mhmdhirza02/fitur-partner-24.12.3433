<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Mencari ID transaksi tersebut di database lokal kita
        $transaction = Transaction::with('event')->where('order_id', $orderId)->first();

        if (!$transaction) {
            // Tetap kembalikan 200 OK agar tes webhook Midtrans (yang menggunakan data dummy) berhasil,
            // dan mencegah Midtrans melakukan retry berulang jika data tidak ditemukan di database.
            return response()->json(['message' => 'Transaction not found, but callback received'], 200);
        }

        // Cegah proses berulang jika status sudah lunas/sukses
        if ($transaction->status === 'settlement' || $transaction->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        // Logika Penerjemahan Status Midtrans API
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $transaction->status = 'success';
                $this->processSuccess($transaction);
            }
        } else if ($transactionStatus == 'settlement') {
            $transaction->status = 'settlement';
            $this->processSuccess($transaction);
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->status = 'failed';
        } else if ($transactionStatus == 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();
        return response()->json(['message' => 'OK']);
    }

       private function processSuccess(Transaction $transaction)
    {
        $event = $transaction->event;
        
        // Jika tiket masih ada dan terhubung dengan data event, kurangi jumlahnya sebanyak 1
        if ($event && $event->stock > 0) {
            $event->stock = $event->stock - 1;
            $event->save();
            
            // Mengirimkan email E-Ticket ke pelanggan
            try {
                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\EventTicketMail($transaction));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email E-Ticket: ' . $e->getMessage());
            }
        } else {
            \Log::warning('Stock habis setelah pembayaran berhasil (Perlu proses refund opsional). Order: ' . $transaction->order_id);
        }
    }
}