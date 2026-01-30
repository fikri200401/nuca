<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders for upcoming bookings (H-1)';

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
        $this->info('Sending booking reminders...');

        // Get bookings for tomorrow
        $tomorrow = Carbon::tomorrow()->toDateString();

        $bookings = Booking::whereIn('status', ['auto_approved', 'deposit_confirmed'])
            ->whereDate('booking_date', $tomorrow)
            ->with(['user', 'treatment', 'doctor'])
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            $this->whatsappService->sendBookingReminder($booking);
            
            $count++;
            $this->info("Reminder sent: Booking #{$booking->booking_code} - {$booking->user->name}");
        }

        $this->info("Total reminders sent: {$count}");

        return 0;
    }
}
