<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Services\BookingService;
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

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        parent::__construct();
        $this->bookingService = $bookingService;
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
            $result = $this->bookingService->expireDeposit($deposit->id);

            if ($result['success']) {
                $count++;
                $this->info("Expired: Booking #{$result['booking']->booking_code}");
            }
        }

        $this->info("Total expired deposits: {$count}");

        return 0;
    }
}
