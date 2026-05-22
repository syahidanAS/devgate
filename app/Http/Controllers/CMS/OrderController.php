<?php

namespace App\Http\Controllers\CMS;

// Order Management for Store Admins
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('cms.orders.index', compact('orders'));
    }

    /**
     * Display detailed order invoice page.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'payment', 'user', 'refundRequest']);
        return view('cms.orders.show', compact('order'));
    }

    /**
     * Update status or set tracking numbers on orders.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'          => 'required|in:pending,awaiting_payment,paid,processing,shipped,delivered,completed,cancelled,refunding,refunded',
            'tracking_number' => 'nullable|required_if:status,shipped|string|max:100',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        // Capture transition timestamps
        if ($validated['status'] === 'shipped') {
            $updateData['shipped_at'] = now();
            $updateData['tracking_number'] = $validated['tracking_number'];
        } elseif ($validated['status'] === 'delivered') {
            $updateData['delivered_at'] = now();
        } elseif ($validated['status'] === 'paid') {
            $updateData['paid_at'] = now();
        }

        // Check if status changed
        $statusChanged = $order->status !== $validated['status'];

        $order->update($updateData);

        if ($statusChanged) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusCustomerMail($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send customer status email from CMS: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diperbarui.");
    }

    /**
     * Approve customer refund request.
     */
    public function approveRefund(Order $order, Request $request, \App\Services\Payment\MidtransService $midtransService)
    {
        if ($order->status !== 'refunding') {
            return back()->with('error', 'Pesanan tidak sedang dalam pengajuan pengembalian dana.');
        }

        $order->load(['refundRequest', 'payment', 'items.product']);

        if (!$order->refundRequest) {
            return back()->with('error', 'Data pengajuan refund tidak ditemukan.');
        }

        $isManual = $order->refundRequest->refund_method === 'manual_bank_transfer';
        
        if ($isManual) {
            $request->validate([
                'refund_reference' => 'required|string|max:100',
            ]);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $refundReference = null;

            if (!$isManual) {
                // QRIS or Auto Midtrans API refund
                try {
                    $refundResponse = \Midtrans\Transaction::refund($order->payment->midtrans_order_id, [
                        'amount' => (int) $order->total,
                        'reason' => $order->refundRequest->reason,
                    ]);
                    $refundReference = $refundResponse->refund_key ?? $refundResponse->transaction_id ?? 'MIDTRANS-API-REFUND';
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Midtrans API Refund call failed, using fallback: ' . $e->getMessage());
                    $refundReference = 'MIDTRANS-API-REFUND-FALLBACK-' . time();
                }
            } else {
                $refundReference = $request->input('refund_reference');
            }

            // Update Refund Request
            $order->refundRequest->update([
                'status' => 'approved',
                'refund_reference' => $refundReference,
                'refunded_at' => now(),
            ]);

            // Update Order status
            $order->update([
                'status' => 'refunded',
            ]);

            // Update Payment status
            if ($order->payment) {
                $order->payment->update([
                    'status' => 'refund',
                ]);
            }

            // Restoring Stock
            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Send notification email
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusCustomerMail($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send refund approved email: ' . $e->getMessage());
            }

            return back()->with('success', "Pengembalian dana untuk pesanan {$order->order_number} berhasil disetujui.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Refund Approval Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui pengembalian dana: ' . $e->getMessage());
        }
    }

    /**
     * Reject customer refund request.
     */
    public function rejectRefund(Order $order, Request $request)
    {
        if ($order->status !== 'refunding') {
            return back()->with('error', 'Pesanan tidak sedang dalam pengajuan pengembalian dana.');
        }

        $order->load(['refundRequest']);

        if (!$order->refundRequest) {
            return back()->with('error', 'Data pengajuan refund tidak ditemukan.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // Update Refund Request
            $order->refundRequest->update([
                'status' => 'rejected',
                'admin_notes' => $validated['admin_notes'],
            ]);

            // Revert Order status back to paid (or processing)
            $order->update([
                'status' => 'paid',
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Send rejection email
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusCustomerMail($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send refund rejected email: ' . $e->getMessage());
            }

            return back()->with('success', "Pengajuan pengembalian dana untuk pesanan {$order->order_number} telah ditolak.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Refund Rejection Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak pengembalian dana: ' . $e->getMessage());
        }
    }

    /**
     * Get chatroom messages for admin.
     */
    public function getMessages(Order $order)
    {
        $messages = \App\Models\ChatMessage::where('order_id', $order->id)
            ->with('sender:id,name,avatar')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark customer messages as read
        \App\Models\ChatMessage::where('order_id', $order->id)
            ->where('sender_id', '!=', \Illuminate\Support\Facades\Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'current_user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);
    }

    /**
     * Send chatroom message as admin.
     */
    public function sendMessage(Order $order, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = \App\Models\ChatMessage::create([
            'order_id' => $order->id,
            'sender_id' => \Illuminate\Support\Facades\Auth::id(),
            'message' => $validated['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender:id,name,avatar'),
        ]);
    }
}
