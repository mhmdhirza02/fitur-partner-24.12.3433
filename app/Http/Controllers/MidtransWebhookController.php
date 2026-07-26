<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            // Jika status transaksi dibatalkan, ditolak, atau expired (misal 15 menit),
            // dan status di lokal masih pending/challenge (stok sedang ditahan),
            // maka lepaskan tiket (+1) agar dapat direbut pembeli lain.
            if (in_array(strtolower($transaction->status), ['pending', 'challenge'])) {
                $this->releaseReservedTicket($transaction);
            }
            $transaction->status = ($transactionStatus == 'expire') ? 'expired' : 'failed';
        } else if ($transactionStatus == 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();
        return response()->json(['message' => 'OK']);
    }

    private function processSuccess(Transaction $transaction)
    {
        // CATATAN: Stok TIDAK dikurangi lagi di sini karena stok sudah ditahan (reserved) sejak tombol Checkout diklik.
        // Mengirimkan email E-Ticket ke pelanggan
        try {
            \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\EventTicketMail($transaction));
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email E-Ticket: ' . $e->getMessage());
        }
    }

    private function releaseReservedTicket(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $event = Event::where('id', $transaction->event_id)->lockForUpdate()->first();
            if ($event) {
                $event->increment('stock');
            }
        });
        \Log::info("Tiket untuk Order ID {$transaction->order_id} dilepaskan kembali (+1) karena pembayaran expired/batal.");
    }
}