<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Booking;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class ExpireDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-expire deposits that passed 24 hours deadline';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired deposits...');

        // Get all pending deposits that passed deadline
        $expiredDeposits = Deposit::where('status', 'pending')
            ->where('deadline_at', '<', now())
            ->with('booking')
            ->get();

        $count = 0;

        foreach ($expiredDeposits as $deposit) {
            // Update deposit status
            $deposit->update(['status' => 'expired']);

            // Update booking status
            $deposit->booking->update(['status' => 'expired']);

            // Send notification to customer
            $this->whatsappService->sendDepositExpired($deposit->booking);

            $count++;
            $this->info("Expired: Booking #{$deposit->booking->booking_code}");
        }

        $this->info("Total expired deposits: {$count}");

        return 0;
    }
}
