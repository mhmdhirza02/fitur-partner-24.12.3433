<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Request $request, Event $event)
    {
        $selectedTier = null;
        if ($request->filled('tier_id')) {
            $selectedTier = \App\Models\TicketTier::where('event_id', $event->id)->where('id', $request->tier_id)->first();
            if ($selectedTier && ($selectedTier->stock <= 0 || $selectedTier->status !== 'active')) {
                return redirect()->route('events.show', $event->id)->with('error', 'Mohon maaf, kategori tiket terpilih tidak tersedia atau sudah habis.');
            }
        } elseif ($event->stock <= 0) {
            return redirect()->route('events.show', $event->id)->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        return view('checkout.create', compact('event', 'categories', 'selectedTier'));
    }

    public function checkVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'price' => 'required|numeric',
        ]);

        $voucher = \App\Models\Voucher::where('code', strtoupper(trim($request->code)))
            ->where(function($q) use ($request) {
                $q->whereNull('event_id')->orWhere('event_id', $request->event_id);
            })
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak ditemukan.'], 200);
        }

        $discount = $voucher->calculateDiscount($request->price);
        if ($discount <= 0) {
            return response()->json(['success' => false, 'message' => 'Voucher tidak valid, sudah habis kuotanya, atau belum memenuhi syarat.'], 200);
        }

        $finalPrice = max(0, $request->price - $discount);

        return response()->json([
            'success' => true,
            'voucher_code' => $voucher->code,
            'discount_amount' => $discount,
            'discount_formatted' => '- Rp ' . number_format($discount, 0, ',', '.'),
            'final_price' => $finalPrice,
            'final_price_formatted' => 'Rp ' . number_format($finalPrice + ($finalPrice > 0 ? 5000 : 0), 0, ',', '.'),
            'message' => 'Voucher "' . $voucher->name . '" berhasil diterapkan!'
        ]);
    }

    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Merekam Transaksi & Menahan (Reserve) Stok Tiket secara Atomik dengan DB Transaction & Pessimistic Locking
        try {
            $transaction = DB::transaction(function () use ($request, $event, &$orderId, &$totalPrice) {
                // Gunakan lockForUpdate untuk mencegah Race Condition saat pengecekan dan pengurangan stok
                $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

                if (!$lockedEvent || $lockedEvent->stock <= 0) {
                    throw new \Exception('Mohon maaf, tiket untuk acara ini sudah habis.');
                }

                $ticketTierId = null;
                $ticketTierName = null;
                $basePrice = $lockedEvent->price;

                if ($request->filled('tier_id')) {
                    $lockedTier = \App\Models\TicketTier::where('event_id', $event->id)
                        ->where('id', $request->tier_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$lockedTier || $lockedTier->stock <= 0 || $lockedTier->status !== 'active') {
                        throw new \Exception('Mohon maaf, kategori tiket terpilih tidak tersedia atau sudah habis.');
                    }

                    $lockedTier->decrement('stock');
                    $ticketTierId = $lockedTier->id;
                    $ticketTierName = $lockedTier->name;
                    $basePrice = $lockedTier->price;
                }

                // Sesaat setelah pengunjung klik Checkout, langsung "tahan" (reserve) stok tiket (-1)
                $lockedEvent->decrement('stock');

                // Handle Voucher Diskon
                $voucherCode = null;
                $discountAmount = 0;
                if ($request->filled('voucher_code')) {
                    $voucher = \App\Models\Voucher::where('code', strtoupper(trim($request->voucher_code)))
                        ->where(function($q) use ($event) {
                            $q->whereNull('event_id')->orWhere('event_id', $event->id);
                        })
                        ->lockForUpdate()
                        ->first();

                    if ($voucher) {
                        $disc = $voucher->calculateDiscount($basePrice);
                        if ($disc > 0) {
                            $voucher->increment('used_count');
                            $voucherCode = $voucher->code;
                            $discountAmount = $disc;
                            $basePrice = max(0, $basePrice - $discountAmount);
                        }
                    }
                }

                // 3. Generate Kode TRX (Unik)
                $orderId = 'TRX-' . time() . '-' . Str::random(5);
                
                // Percabangan logika khusus: jika harga tiket acara diatur Rp 0 (Acara Gratis)
                $isFreeEvent = ($basePrice <= 0);
                $totalPrice = $isFreeEvent ? 0 : ($basePrice + 5000); // Tanpa biaya admin jika gratis
                $initialStatus = $isFreeEvent ? 'success' : 'Pending';

                // 4. Merekam Transaksi ke Database
                return Transaction::create([
                    'event_id' => $lockedEvent->id,
                    'ticket_tier_id' => $ticketTierId,
                    'ticket_tier_name' => $ticketTierName,
                    'order_id' => $orderId,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'total_price' => $totalPrice,
                    'voucher_code' => $voucherCode,
                    'discount_amount' => $discountAmount,
                    'status' => $initialStatus,
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        // Bypass Transaksi untuk Acara Gratis (Free Events): langsung ke rute sukses & cetak E-Ticket
        if ($transaction->total_price == 0 || $event->price == 0) {
            try {
                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                    ->send(new \App\Mail\EventTicketMail($transaction));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email E-Ticket untuk acara gratis: ' . $e->getMessage());
            }

            return redirect()->route('checkout.success', $transaction->order_id);
        }

        // --- INTEGRASI SNAP MIDTRANS ---
        
        // Konfigurasi Kredensial Environment Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false); // Mode Sandbox!
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Susun Paket Array Data Transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => 15, // Batas waktu pembayaran 15 menit
            ],
        ];

        try {
            // Perintah Tembak Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Update rekaman kita bahwa transaksi terkait sudah memiliki id token pelunasan
            $transaction->update(['snap_token' => $snapToken]);
            
            // Redirect ke halaman antarmuka pembayaran final pelanggan
            return redirect()->route('checkout.payment', $transaction->order_id);
            
        } catch (\Exception $e) {
            // Jika gagal berkomunikasi dengan Midtrans, lepaskan kembali stok tiket (+1) yang sudah ditahan agar dapat direbut pembeli lain
            DB::transaction(function () use ($transaction) {
                $lockedEvent = Event::where('id', $transaction->event_id)->lockForUpdate()->first();
                if ($lockedEvent) {
                    $lockedEvent->increment('stock');
                }
                if ($transaction->ticket_tier_id) {
                    \App\Models\TicketTier::where('id', $transaction->ticket_tier_id)->increment('stock');
                }
                if ($transaction->voucher_code) {
                    \App\Models\Voucher::where('code', $transaction->voucher_code)->decrement('used_count');
                }
                $transaction->update(['status' => 'Failed']);
            });

            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
         // Mengambil daftar kategori untuk keperluan menu footer
         $categories = \App\Models\Category::all();

         $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
         return view('checkout.payment', compact('transaction','categories'));
    }

     public function success($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        
        // Jika acara gratis atau transaksi sudah berstatus success/settlement, bypass pengecekan Midtrans
        if ($transaction->total_price == 0 || ($transaction->event && $transaction->event->price == 0) || in_array(strtolower($transaction->status), ['success', 'settlement'])) {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // Konfigurasi Midtrans untuk mengecek status transaksi langsung ke API
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            // Mengecek status pesanan secara mandiri (Bypass)
            $status = \Midtrans\Transaction::status($order_id);
            
            if ($status) {
                // Mengambil nilai status transaksi
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');
                
                // Jika API Midtrans mengonfirmasi bahwa transaksi telah berhasil (settlement / capture)
                if (in_array($trx_status, ['settlement', 'capture'])) {
                    // Hanya lakukan update jika status di database lokal masih 'pending' atau 'challenge' (indikasi Webhook tidak masuk)
                    if (in_array(strtolower($transaction->status), ['pending', 'challenge'])) {
                        $transaction->update(['status' => 'success']);
                        
                        // CATATAN: Stok TIDAK dikurangi lagi di sini karena stok sudah ditahan (reserved) langsung sejak tombol Checkout diklik.
                        
                        try {
                            \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                                ->send(new \App\Mail\EventTicketMail($transaction));
                        } catch (\Exception $e) {
                            \Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error dari API Midtrans (transaksi tidak valid), kembalikan ke beranda
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}

