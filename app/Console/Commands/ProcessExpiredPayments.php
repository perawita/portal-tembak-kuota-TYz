<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentHistory;
use Carbon\Carbon;

class ProcessExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:process-expired';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process expired payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mengambil pembayaran yang sudah kadaluarsa dan belum dibayar
        $expiredPayments = PaymentHistory::where('expired_at', '<', Carbon::now())
            ->where('status', '!=', 'Sudah dibayar')
            ->get();

        foreach ($expiredPayments as $payment) {
            $payment->update(['status' => 'expired']);
        }
        
        // Menampilkan pesan informasi
        $this->info('Expired payments processed successfully.');
    }
}
