<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Payment;
use App\Services\Payment\MidtransService;
use App\Mail\PaymentReminderMail;
use App\Mail\PaymentExpiredMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:process-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process unpaid payment reminders and automatically cancel expired ones.';

    /**
     * Execute the console command.
     */
    public function handle(MidtransService $midtransService)
    {
        $now = now();

        // 1. Process Reminders
        $pendingReminders = Payment::where('status', 'pending')
            ->where('expired_at', '>', $now)
            ->whereNull('reminder_email_sent_at')
            ->with('order.user')
            ->get();

        foreach ($pendingReminders as $payment) {
            $sendReminder = false;
            
            if ($payment->payment_method === 'qris') {
                // QRIS: 5 minutes or less before expiration
                if ($payment->expired_at->lte($now->copy()->addMinutes(5))) {
                    $sendReminder = true;
                }
            } else {
                // Bank VAs: 2 hours or less before expiration
                if ($payment->expired_at->lte($now->copy()->addHours(2))) {
                    $sendReminder = true;
                }
            }

            if ($sendReminder) {
                try {
                    Mail::to($payment->order->user->email)->send(new PaymentReminderMail($payment->order));
                    $payment->update([
                        'reminder_email_sent_at' => now(),
                    ]);
                    $this->info("Reminder sent for Order #{$payment->order->order_number}");
                    Log::info("Reminder sent for Order #{$payment->order->order_number}");
                } catch (\Exception $e) {
                    Log::error("Failed to send payment reminder for Order ID {$payment->order_id}: " . $e->getMessage());
                }
            }
        }

        // 2. Process Expirations
        $expiredPayments = Payment::where('status', 'pending')
            ->where('expired_at', '<=', $now)
            ->whereNull('expired_email_sent_at')
            ->with('order.user')
            ->get();

        foreach ($expiredPayments as $payment) {
            DB::beginTransaction();
            try {
                $order = $payment->order;

                // Call Midtrans cancel transaction
                $midtransService->cancelTransaction($order);

                // Update payment locally to 'expire'
                $payment->update([
                    'status' => 'expire',
                    'expired_email_sent_at' => now(),
                ]);

                // Update order locally to 'cancelled'
                $order->update([
                    'status' => 'cancelled',
                ]);

                DB::commit();

                // Send expired notification email
                Mail::to($order->user->email)->send(new PaymentExpiredMail($order));

                $this->info("Expired and cancelled Order #{$order->order_number}");
                Log::info("Expired and cancelled Order #{$order->order_number}");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to expire payment for Order ID {$payment->order_id}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
