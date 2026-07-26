<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:release-expired {--minutes=15 : Batas waktu kedaluwarsa dalam menit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melepaskan (+1) stok tiket yang ditahan untuk transaksi pending yang telah melampaui batas waktu pembayaran (expired)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $expiryTime = Carbon::now()->subMinutes($minutes);

        // Cari transaksi yang masih berstatus Pending/challenge dan sudah melewati batas waktu
        $expiredTransactions = Transaction::whereIn('status', ['Pending', 'pending', 'challenge'])
            ->where('created_at', '<=', $expiryTime)
            ->get();

        if ($expiredTransactions->isEmpty()) {
            $this->info("Tidak ada transaksi expired yang ditemukan.");
            return 0;
        }

        $count = 0;
        foreach ($expiredTransactions as $transaction) {
            DB::transaction(function () use ($transaction, $minutes) {
                // Gunakan lockForUpdate untuk menghindari race condition saat mengembalikan stok
                $event = Event::where('id', $transaction->event_id)->lockForUpdate()->first();
                if ($event) {
                    $event->increment('stock');
                }

                $transaction->status = 'expired';
                $transaction->save();
            });

            Log::info("Stok tiket untuk order {$transaction->order_id} dilepaskan (+1) karena waktu pembayaran telah habis (expired > {$minutes} menit).");
            $count++;
        }

        $this->info("Berhasil melepaskan stok tiket dari {$count} transaksi yang expired.");
        return 0;
    }
}
