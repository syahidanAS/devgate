<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display authenticated user orders list.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('marketplace.orders.index', compact('orders'));
    }

    /**
     * Display detailed order invoice/receipt page.
     */
    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->firstOrFail();

        return view('marketplace.orders.show', compact('order'));
    }

    /**
     * Mark order as completed once delivered.
     */
    public function complete(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan belum dikirim.');
        }

        $order->update([
            'status' => 'completed',
            'delivered_at' => now(),
        ]);

        return back()->with('success', 'Terima kasih! Pesanan Anda telah selesai.');
    }

    /**
     * Cancel the order if not yet shipped.
     */
    public function cancel(string $orderNumber, \App\Services\Payment\MidtransService $midtransService)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->firstOrFail();

        $eligibleStatuses = ['pending', 'awaiting_payment'];
        if (!in_array($order->status, $eligibleStatuses)) {
            if (in_array($order->status, ['paid', 'processing'])) {
                return back()->with('error', 'Pesanan yang sudah dibayar hanya dapat dibatalkan melalui pengajuan pengembalian dana (refund).');
            }
            return back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $oldStatus = $order->status;

            // 1. Update Order status
            $order->update([
                'status' => 'cancelled',
            ]);

            // 2. Cancel Midtrans transaction if unpaid (awaiting_payment or pending)
            if ($order->payment) {
                $midtransService->cancelTransaction($order);
            }

            \Illuminate\Support\Facades\DB::commit();

            // Dispatch Telegram notification
            \App\Jobs\SendTelegramOrderCancelledNotification::dispatch($order);

            return back()->with('success', 'Pesanan Anda berhasil dibatalkan.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order Cancellation Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Submit a refund request for a paid order.
     */
    public function refund(string $orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['payment'])
            ->firstOrFail();

        if (!in_array($order->status, ['paid', 'processing'])) {
            return back()->with('error', 'Hanya pesanan yang sudah dibayar yang dapat mengajukan pengembalian dana.');
        }

        // Check if refund request already exists
        if ($order->refundRequest()->exists()) {
            return back()->with('error', 'Anda telah mengajukan pengembalian dana untuk pesanan ini.');
        }

        $paymentMethod = $order->payment->payment_method ?? 'qris';
        $isManual = in_array($paymentMethod, ['bca', 'mandiri', 'bni', 'bri']);

        $rules = [
            'reason' => 'required|string|max:1000',
        ];

        if ($isManual) {
            $rules['bank_name'] = 'required|string|max:100';
            $rules['account_number'] = 'required|string|max:50';
            $rules['account_holder'] = 'required|string|max:150';
        }

        $validated = $request->validate($rules);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Create Refund Request
            $refundRequest = \App\Models\RefundRequest::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'refund_method' => $isManual ? 'manual_bank_transfer' : 'midtrans_api',
                'reason' => $validated['reason'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder' => $validated['account_holder'] ?? null,
            ]);

            // Update order status to refunding
            $order->update([
                'status' => 'refunding',
            ]);

            // Create Chat Message representing the refund request
            $chatMessageContent = "🚨 Pengajuan Pembatalan / Refund\n"
                . "Alasan: " . $validated['reason'] . "\n"
                . ($isManual 
                    ? "Metode: Transfer Bank Manual\nBank: " . strtoupper($validated['bank_name']) . "\nNo. Rek: " . $validated['account_number'] . "\nA/N: " . $validated['account_holder']
                    : "Metode: Pengembalian Otomatis QRIS (Midtrans API)");

            \App\Models\ChatMessage::create([
                'order_id' => $order->id,
                'sender_id' => Auth::id(),
                'message' => $chatMessageContent,
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Dispatch Telegram notification
            \App\Jobs\SendTelegramRefundNotification::dispatch($refundRequest);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan pengembalian dana Anda berhasil dikirim dan sedang ditinjau.',
                ]);
            }

            return redirect()->route('orders.show', $order->order_number)
                ->with('success', 'Pengajuan pengembalian dana Anda berhasil dikirim dan sedang ditinjau.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Refund Request Error: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengajukan pengembalian dana: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal mengajukan pengembalian dana: ' . $e->getMessage());
        }
    }

    /**
     * Get chatroom messages for customer.
     */
    public function getMessages(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $messages = \App\Models\ChatMessage::where('order_id', $order->id)
            ->with('sender:id,name,avatar')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark admin messages as read
        \App\Models\ChatMessage::where('order_id', $order->id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'current_user_id' => Auth::id(),
        ]);
    }

    /**
     * Send chatroom message.
     */
    public function sendMessage(string $orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = \App\Models\ChatMessage::create([
            'order_id' => $order->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender:id,name,avatar'),
        ]);
    }
    /**
     * Verify payment status using Midtrans Core API status check.
     */
    public function verify(string $orderNumber, \App\Services\Payment\MidtransService $midtransService)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['payment'])
            ->firstOrFail();

        if ($order->status !== 'awaiting_payment') {
            return back()->with('info', 'Status pesanan ini tidak sedang menunggu pembayaran.');
        }

        try {
            $midtransService->checkPaymentStatus($order);
            
            // Reload the order model to get updated status
            $order->refresh();

            if ($order->status === 'paid') {
                return redirect()->route('orders.show', $order->order_number)
                    ->with('success', 'Pembayaran Anda berhasil diverifikasi! Terima kasih.');
            }

            return redirect()->route('orders.show', $order->order_number)
                ->with('info', 'Pembayaran belum terdeteksi. Silakan tunggu beberapa saat atau selesaikan pembayaran terlebih dahulu.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order verification error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }
}
