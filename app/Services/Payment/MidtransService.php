<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        Config::$clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY'));
        Config::$isProduction = config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Get Snap token for checkout payment redirection.
     */
    public function getSnapToken(Order $order): string
    {
        // Check if snap token already exists and payment is pending
        $existingPayment = Payment::where('order_id', $order->id)->first();
        if ($existingPayment && $existingPayment->snap_token && $existingPayment->status === 'pending') {
            return $existingPayment->snap_token;
        }

        $midtransOrderId = $order->order_number . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->shipping_address['phone'] ?? '',
                'billing_address' => [
                    'first_name'   => $order->user->name,
                    'phone'        => $order->shipping_address['phone'] ?? '',
                    'address'      => $order->shipping_address['address'] ?? '',
                    'city'         => $order->shipping_address['city'] ?? '',
                    'postal_code'  => $order->shipping_address['postal_code'] ?? '',
                ],
                'shipping_address' => [
                    'first_name'   => $order->shipping_address['recipient_name'] ?? $order->user->name,
                    'phone'        => $order->shipping_address['phone'] ?? '',
                    'address'      => $order->shipping_address['address'] ?? '',
                    'city'         => $order->shipping_address['city'] ?? '',
                    'postal_code'  => $order->shipping_address['postal_code'] ?? '',
                ],
            ],
        ];

        // Format items
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product_name, 0, 50),
            ];
        }

        // Add shipping fee if any
        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING_FEE',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        // Add discount if any
        if ($order->discount > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -(int) $order->discount,
                'quantity' => 1,
                'name'     => 'Diskon / Potongan',
            ];
        }

        $params['item_details'] = $itemDetails;

        try {
            $snapToken = Snap::getSnapToken($params);

            // Log or update Payment
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'midtrans_order_id' => $midtransOrderId,
                    'amount'            => $order->total,
                    'status'            => 'pending',
                    'snap_token'        => $snapToken,
                ]
            );

            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Snap Token Exception: ' . $e->getMessage());
            throw new Exception('Gagal membuat snap token pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Create direct transaction using Core API.
     */
    public function createTransaction(Order $order, string $paymentMethod): array
    {
        // Check if payment already exists and is pending
        $existingPayment = Payment::where('order_id', $order->id)->first();
        if ($existingPayment && $existingPayment->status === 'pending') {
            if ($existingPayment->payment_method === $paymentMethod) {
                return [
                    'payment' => $existingPayment,
                    'status' => 'pending',
                ];
            }
        }

        $midtransOrderId = $order->order_number . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->shipping_address['phone'] ?? '',
                'billing_address' => [
                    'first_name'   => $order->user->name,
                    'phone'        => $order->shipping_address['phone'] ?? '',
                    'address'      => $order->shipping_address['address'] ?? '',
                    'city'         => $order->shipping_address['city'] ?? '',
                    'postal_code'  => $order->shipping_address['postal_code'] ?? '',
                ],
                'shipping_address' => [
                    'first_name'   => $order->shipping_address['recipient_name'] ?? $order->user->name,
                    'phone'        => $order->shipping_address['phone'] ?? '',
                    'address'      => $order->shipping_address['address'] ?? '',
                    'city'         => $order->shipping_address['city'] ?? '',
                    'postal_code'  => $order->shipping_address['postal_code'] ?? '',
                ],
            ],
        ];

        // Format items
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product_name, 0, 50),
            ];
        }

        // Add shipping fee if any
        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id'       => 'SHIPPING_FEE',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        // Add discount if any
        if ($order->discount > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -(int) $order->discount,
                'quantity' => 1,
                'name'     => 'Diskon / Potongan',
            ];
        }

        $params['item_details'] = $itemDetails;

        // Apply payment type specific params
        if (in_array($paymentMethod, ['bca', 'bni', 'bri'])) {
            $params['payment_type'] = 'bank_transfer';
            $params['bank_transfer'] = [
                'bank' => $paymentMethod
            ];
        } elseif ($paymentMethod === 'mandiri') {
            $params['payment_type'] = 'echannel';
            $params['echannel'] = [
                'bill_info1' => 'Pembayaran DevGate',
                'bill_info2' => 'Order ' . $order->order_number,
            ];
        } elseif ($paymentMethod === 'qris') {
            $params['payment_type'] = 'qris';
            $params['qris'] = [
                'acquirer' => 'gopay',
            ];
        } else {
            throw new Exception('Metode pembayaran tidak valid.');
        }

        // Set custom expiry duration
        $params['custom_expiry'] = [
            'expiry_duration' => $paymentMethod === 'qris' ? 15 : 24,
            'unit'            => $paymentMethod === 'qris' ? 'minute' : 'hour',
        ];

        $expiredAt = $paymentMethod === 'qris' ? now()->addMinutes(15) : now()->addHours(24);

        try {
            $response = CoreApi::charge($params);

            // Parse response fields
            $vaNumber = null;
            $qrisUrl = null;

            if ($paymentMethod === 'mandiri') {
                $vaNumber = $response->bill_key ?? null;
            } elseif (in_array($paymentMethod, ['bca', 'bni', 'bri'])) {
                if (isset($response->va_numbers[0]->va_number)) {
                    $vaNumber = $response->va_numbers[0]->va_number;
                }
            } elseif ($paymentMethod === 'qris') {
                if (isset($response->actions)) {
                    foreach ($response->actions as $action) {
                        if ($action->name === 'generate-qr-code') {
                            $qrisUrl = $action->url;
                            break;
                        }
                    }
                }
            }

            // Create or update Payment
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'midtrans_order_id'       => $midtransOrderId,
                    'midtrans_transaction_id' => $response->transaction_id ?? null,
                    'amount'                  => $order->total,
                    'payment_type'            => $params['payment_type'],
                    'payment_method'          => $paymentMethod,
                    'status'                  => 'pending',
                    'va_number'               => $vaNumber,
                    'qris_url'                => $qrisUrl,
                    'payload'                 => (array) $response,
                    'expired_at'              => $expiredAt,
                ]
            );

            return [
                'payment' => $payment,
                'status'  => 'success',
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Core API Exception: ' . $e->getMessage());
            throw new Exception('Gagal membuat transaksi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment status synchronization from Midtrans notifications.
     */
    public function handleNotification(array $payload): bool
    {
        Log::info('Midtrans Webhook payload: ', $payload);

        $transactionStatus = $payload['transaction_status'] ?? '';
        $type = $payload['payment_type'] ?? '';
        $midtransOrderId = $payload['order_id'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $transactionId = $payload['transaction_id'] ?? '';

        // Extract order number (remove the timestamp appended after the last dash)
        $lastDashPos = strrpos($midtransOrderId, '-');
        $orderNumber = $lastDashPos !== false ? substr($midtransOrderId, 0, $lastDashPos) : $midtransOrderId;

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            // For Midtrans test notifications from dashboard, return true so it doesn't fail.
            if (str_starts_with($midtransOrderId, 'payment_notif_test')) {
                 return true;
            }
            Log::error("Order with number {$orderNumber} not found for webhook notification.");
            return false;
        }

        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            Log::error("Payment record not found for Order ID {$order->id}.");
            return false;
        }

        // Update payment info
        $paymentStatus = 'pending';
        $orderStatus = 'awaiting_payment';

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $paymentStatus = 'pending';
                    $orderStatus = 'awaiting_payment';
                } else {
                    $paymentStatus = 'settlement';
                    $orderStatus = 'paid';
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $paymentStatus = 'settlement';
            $orderStatus = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $paymentStatus = 'pending';
            $orderStatus = 'awaiting_payment';
        } elseif ($transactionStatus == 'deny') {
            $paymentStatus = 'deny';
            $orderStatus = 'cancelled';
        } elseif ($transactionStatus == 'expire') {
            $paymentStatus = 'expire';
            $orderStatus = 'cancelled';
        } elseif ($transactionStatus == 'cancel') {
            $paymentStatus = 'cancel';
            $orderStatus = 'cancelled';
        }

        // Get VA number or bill key
        $vaNumber = null;
        if (isset($payload['va_numbers'][0]['va_number'])) {
            $vaNumber = $payload['va_numbers'][0]['va_number'];
        } elseif (isset($payload['permata_va_number'])) {
            $vaNumber = $payload['permata_va_number'];
        } elseif (isset($payload['bill_key'])) {
            $vaNumber = $payload['bill_key'];
        }

        // Update payment log
        $payment->update([
            'midtrans_transaction_id' => $transactionId,
            'payment_type'            => $type,
            'payment_method'          => $payload['payment_type'] ?? null,
            'status'                  => $paymentStatus,
            'va_number'               => $vaNumber,
            'payload'                 => $payload,
            'paid_at'                 => $paymentStatus === 'settlement' ? now() : null,
        ]);

        // Update order status
        $orderData = [
            'status' => $orderStatus,
        ];
        if ($orderStatus === 'paid') {
            $orderData['paid_at'] = now();
            // Deduct stock
            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_stock) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            // Notify admin-marketplace users about the new paid order
            try {
                $admins = User::role('admin-marketplace')->get();
                NotificationFacade::send($admins, new NewOrderNotification($order));
            } catch (Exception $e) {
                Log::warning('Failed to send order notification: ' . $e->getMessage());
            }
        }
        
        // Check if status actually changed
        $statusChanged = $order->status !== $orderStatus;
        
        $order->update($orderData);

        if ($statusChanged) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderStatusCustomerMail($order));
            } catch (\Exception $e) {
                Log::error('Failed to send customer status email: ' . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Cancel an active transaction on Midtrans.
     */
    public function cancelTransaction(Order $order): bool
    {
        if (!$order->payment || !$order->payment->midtrans_order_id) {
            return false;
        }

        try {
            Transaction::cancel($order->payment->midtrans_order_id);
            $order->payment->update(['status' => 'cancel']);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to cancel Midtrans transaction: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check current transaction status on Midtrans and update local database.
     */
    public function checkPaymentStatus(Order $order): bool
    {
        if (!$order->payment || !$order->payment->midtrans_order_id) {
            return false;
        }

        try {
            $statusResponse = Transaction::status($order->payment->midtrans_order_id);
            // Convert stdClass/object to array so it can be handled by handleNotification
            $payload = json_decode(json_encode($statusResponse), true);
            
            if (is_array($payload)) {
                return $this->handleNotification($payload);
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Failed to check payment status from Midtrans: ' . $e->getMessage());
            return false;
        }
    }
}
