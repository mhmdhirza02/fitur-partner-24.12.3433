<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $transactionQuery = Transaction::query();
        $eventQuery = Event::query();

        if ($user->role === 'partner') {
            $transactionQuery->whereHas('event', function ($q) use ($user) {
                $q->where('partner_id', $user->partner_id);
            });
            $eventQuery->where('partner_id', $user->partner_id);
        }

        // 1. Menjumlahkan semua nominal total_price dari kolom Transaksi Lunas
        $totalRevenue = (clone $transactionQuery)->whereIn('status', ['settlement', 'success'])->sum('total_price');
        
        // 2. Menghitung Berapa orang tamu yang tiketnya sudah Lunas
        $ticketsSold = (clone $transactionQuery)->whereIn('status', ['settlement', 'success'])->count();
        
        // 3. Menghitung Jumlah Acara Mendatang yang aktif diselenggarakan
        $activeEvents = (clone $eventQuery)->where('date', '>=', now())->count();
        
        // 4. Menghitung Transaksi Ngadat (Status belum dibayar pelanggan / Expired)
        $pendingOrders = (clone $transactionQuery)->where('status', 'pending')->count();
        
        // 5. Menyertakan 5 daftar riwayat pesanan (History) paling mutakhir di panel
        $recentTransactions = (clone $transactionQuery)->with('event')->latest()->take(5)->get();
        
        // 6. Data Grafik: Pertumbuhan Penyelenggaraan Event (6 Bulan Terakhir)
        $chartLabels = [];
        $eventData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('F Y'); // misal: "July 2026"
            
            // Hitung event yang dibuat pada bulan tersebut
            $count = (clone $eventQuery)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
                
            $eventData[] = $count;
        }

        return view('admin.dashboard', compact('totalRevenue', 'ticketsSold', 'activeEvents', 'pendingOrders', 'recentTransactions', 'chartLabels', 'eventData'));
    }
}