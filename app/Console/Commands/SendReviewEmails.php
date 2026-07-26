<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewRequestMail;
use Carbon\Carbon;

class SendReviewEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-review-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email permintaan review ke pembeli 1 hari setelah event selesai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();

        // Cari transaksi sukses untuk event yang selesai kemarin
        $transactions = Transaction::where('status', 'success')
            ->whereHas('event', function ($q) use ($yesterdayStart, $yesterdayEnd) {
                $q->whereBetween('date', [$yesterdayStart, $yesterdayEnd]);
            })
            ->doesntHave('review') // yang belum direview
            ->get();

        $count = 0;
        foreach ($transactions as $transaction) {
            Mail::to($transaction->customer_email)->send(new ReviewRequestMail($transaction));
            $count++;
        }

        $this->info("Berhasil mengirim {$count} email permintaan review.");
    }
}
