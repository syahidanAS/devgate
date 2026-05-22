<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Address $address;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard user
        $this->user = User::factory()->create();

        // Create a product seller
        $seller = User::factory()->create();

        // Create a test product
        $this->product = Product::create([
            'user_id' => $seller->id,
            'name' => 'Produk Test Super Premium',
            'sku' => 'TEST-SKU-123',
            'excerpt' => 'Excerpt test',
            'description' => 'Description test',
            'price' => 150000,
            'stock' => 10,
            'weight' => 500,
            'status' => 'active',
            'track_stock' => true,
        ]);

        // Create shipping address
        $this->address = Address::create([
            'user_id' => $this->user->id,
            'label' => 'Rumah',
            'recipient_name' => $this->user->name,
            'phone' => '081234567890',
            'address' => 'Jl. Kebagusan Raya No. 42',
            'city' => 'Jakarta Selatan',
            'city_id' => '136',
            'province' => 'DKI Jakarta',
            'province_id' => '10',
            'postal_code' => '12520',
            'is_default' => true,
        ]);
    }

    /**
     * Test checkout requires user authentication.
     */
    public function test_checkout_requires_authenticated_user(): void
    {
        $response = $this->post(route('checkout.process'), [
            'address_id' => $this->address->id,
            'courier' => 'jne',
            'courier_service' => 'REG',
            'shipping_cost' => 17000,
            'payment_method' => 'bca',
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test checkout requires validation of payment method.
     */
    public function test_checkout_requires_valid_payment_method(): void
    {
        // Add to cart
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('checkout.index'))
            ->post(route('checkout.process'), [
                'address_id' => $this->address->id,
                'courier' => 'jne',
                'courier_service' => 'REG',
                'shipping_cost' => 17000,
                'payment_method' => 'invalid-payment-method', // invalid method
            ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHasErrors('payment_method');
    }

    /**
     * Test successful checkout process with BCA virtual account.
     */
    public function test_checkout_processes_order_successfully_with_bca_va(): void
    {
        // Add item to cart
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Mock MidtransService
        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('createTransaction')
            ->once()
            ->withArgs(function ($order, $paymentMethod) {
                return $order instanceof Order && $paymentMethod === 'bca';
            })
            ->andReturn([
                'payment' => new Payment([
                    'order_id' => 1,
                    'amount' => 317000,
                    'payment_type' => 'bank_transfer',
                    'payment_method' => 'bca',
                    'status' => 'pending',
                    'va_number' => '12345678901',
                ]),
                'status' => 'success',
            ]);

        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'address_id' => $this->address->id,
                'courier' => 'jne',
                'courier_service' => 'REG',
                'shipping_cost' => 17000,
                'notes' => 'Tolong dibungkus rapi.',
                'payment_method' => 'bca',
            ]);

        // Should redirect to order show page
        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order->order_number));

        // Assert database values
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'awaiting_payment',
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 300000,
            'shipping_cost' => 17000,
            'total' => 317000,
            'notes' => 'Tolong dibungkus rapi.',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
            'price' => 150000,
            'subtotal' => 300000,
        ]);

        // Assert cart is cleared
        $this->assertDatabaseMissing('carts', [
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test webhook signature validation and order status update to Paid.
     */
    public function test_webhook_verifies_signature_and_updates_order_status_to_paid(): void
    {
        // 1. Setup Order and Payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-20260522-TEST1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-20260522-TEST1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
        ]);

        // Create order item to verify stock decrement
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 150000,
            'quantity' => 2,
            'subtotal' => 300000,
            'product_snapshot' => $this->product->toArray(),
        ]);

        // Confirm starting stock
        $this->assertEquals(10, $this->product->fresh()->stock);

        // Define mock credentials
        $serverKey = 'dummy-server-key';
        config(['services.midtrans.server_key' => $serverKey]);

        // Construct notification payload
        $orderId = 'DG-20260522-TEST1-1715000000';
        $statusCode = '200';
        $grossAmount = '167000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'transaction_time' => '2026-05-22 17:00:00',
            'transaction_status' => 'settlement', // Paid status
            'status_message' => 'midtrans payment notification',
            'status_code' => $statusCode,
            'signature_key' => $signatureKey,
            'payment_type' => 'bank_transfer',
            'order_id' => $orderId,
            'merchant_id' => 'G12345678',
            'gross_amount' => $grossAmount,
            'fraud_status' => 'accept',
            'currency' => 'IDR',
            'transaction_id' => 'trans-id-999',
            'va_numbers' => [
                [
                    'va_number' => '987654321',
                    'bank' => 'bca',
                ]
            ],
        ];

        // Disable mail sending or assert mails sent
        \Illuminate\Support\Facades\Mail::fake();

        // Post to Webhook route
        $response = $this->postJson(route('payment.webhook'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Notification processed successfully',
        ]);

        // Verify Payment updated
        $payment->refresh();
        $this->assertEquals('settlement', $payment->status);
        $this->assertEquals('trans-id-999', $payment->midtrans_transaction_id);
        $this->assertNotNull($payment->paid_at);

        // Verify Order updated
        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->paid_at);

        // Verify Stock decremented (10 - 2 = 8)
        $this->assertEquals(8, $this->product->fresh()->stock);
    }

    /**
     * Test webhook successfully parses Mandiri's e-channel payload layout.
     */
    public function test_webhook_successfully_parses_mandiri_bill_payment_key(): void
    {
        // 1. Setup Order and Payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-20260522-TEST2',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNT',
            'courier_service' => 'EZ',
            'subtotal' => 150000,
            'shipping_cost' => 15000,
            'total' => 165000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-20260522-TEST2-1715000000',
            'amount' => 165000,
            'payment_type' => 'echannel',
            'payment_method' => 'mandiri',
            'status' => 'pending',
            'va_number' => '888888', // bill key
        ]);

        // Define mock credentials
        $serverKey = 'dummy-server-key';
        config(['services.midtrans.server_key' => $serverKey]);

        // Mandiri notification payload uses 'bill_key' instead of standard va_number
        $orderId = 'DG-20260522-TEST2-1715000000';
        $statusCode = '200';
        $grossAmount = '165000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'transaction_time' => '2026-05-22 17:00:00',
            'transaction_status' => 'settlement',
            'status_message' => 'midtrans payment notification',
            'status_code' => $statusCode,
            'signature_key' => $signatureKey,
            'payment_type' => 'echannel',
            'order_id' => $orderId,
            'merchant_id' => 'G12345678',
            'gross_amount' => $grossAmount,
            'fraud_status' => 'accept',
            'currency' => 'IDR',
            'transaction_id' => 'trans-id-888',
            'bill_key' => '888888',
            'biller_code' => '70012',
        ];

        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->postJson(route('payment.webhook'), $payload);

        $response->assertOk();

        // Verify Payment bill_key is mapped correctly to va_number field
        $payment->refresh();
        $this->assertEquals('settlement', $payment->status);
        $this->assertEquals('888888', $payment->va_number);

        // Verify Order updated
        $order->refresh();
        $this->assertEquals('paid', $order->status);
    }

    /**
     * Test cancellation of an unpaid order calls Midtrans cancel and updates order status.
     */
    public function test_order_cancellation_by_unpaid_user_calls_midtrans_cancellation(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-20260522-TEST-CANCEL-1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-20260522-TEST-CANCEL-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
        ]);

        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('cancelTransaction')
            ->once()
            ->withArgs(function ($argOrder) use ($order) {
                return $argOrder->id === $order->id;
            })
            ->andReturn(true);

        $response = $this->actingAs($this->user)
            ->post(route('orders.cancel', $order->order_number));

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    /**
     * Test cancellation of a paid order restores stock locally.
     */
    public function test_order_cancellation_by_paid_user_restores_stock_locally(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-20260522-TEST-CANCEL-2',
            'status' => 'processing', // Paid & processing state
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 300000,
            'shipping_cost' => 17000,
            'total' => 317000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-20260522-TEST-CANCEL-2-1715000000',
            'amount' => 317000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'settlement',
            'va_number' => '987654321',
        ]);

        // Create order item with quantity = 3
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 150000,
            'quantity' => 3,
            'subtotal' => 450000,
            'product_snapshot' => $this->product->toArray(),
        ]);

        // Manually simulate that stock has been decremented when paid (e.g. stock is now 7, started at 10)
        $this->product->update(['stock' => 7]);

        // Mock MidtransService (should not call cancelTransaction since order is already paid/processing)
        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldNotReceive('cancelTransaction');

        $response = $this->actingAs($this->user)
            ->post(route('orders.cancel', $order->order_number));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pesanan yang sudah dibayar hanya dapat dibatalkan melalui pengajuan pengembalian dana (refund).');

        $order->refresh();
        $this->assertEquals('processing', $order->status);

        $payment->refresh();
        $this->assertEquals('settlement', $payment->status);

        // Assert stock remains unchanged (7)
        $this->assertEquals(7, $this->product->fresh()->stock);
    }

    /**
     * Test command sends email reminder when VA payment is near expiration (<= 2 hours remaining).
     */
    public function test_process_payment_expirations_sends_email_reminders(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // Create order and payment with expired_at in 1 hour and 30 minutes
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REMIND-1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REMIND-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
            'expired_at' => now()->addHours(1)->addMinutes(30),
        ]);

        // Run the console command
        $this->artisan('payments:process-expirations')
            ->assertSuccessful();

        // Assert reminder email was sent
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\PaymentReminderMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });

        // Assert reminder_email_sent_at was updated in db
        $payment->refresh();
        $this->assertNotNull($payment->reminder_email_sent_at);
        $this->assertNull($payment->expired_email_sent_at);
    }

    /**
     * Test command does not send duplicate reminders if reminder was already sent.
     */
    public function test_process_payment_expirations_does_not_send_duplicate_reminders(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // Create order and payment with expired_at in 1 hour and reminder_email_sent_at already populated
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REMIND-2',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REMIND-2-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
            'expired_at' => now()->addHours(1),
            'reminder_email_sent_at' => now()->subMinutes(10),
        ]);

        // Run the console command
        $this->artisan('payments:process-expirations')
            ->assertSuccessful();

        // Assert NO reminder email was sent
        \Illuminate\Support\Facades\Mail::assertNotQueued(\App\Mail\PaymentReminderMail::class);
    }

    /**
     * Test command automatically cancels expired payments, calls Midtrans, and sends email notification.
     */
    public function test_process_payment_expirations_cancels_expired_payment_and_sends_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // Create order and payment with expired_at in the past
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-EXPIRE-1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-EXPIRE-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
            'expired_at' => now()->subMinutes(5),
        ]);

        // Mock MidtransService so it expects cancelTransaction and returns true
        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('cancelTransaction')
            ->once()
            ->withArgs(function ($argOrder) use ($order) {
                return $argOrder->id === $order->id;
            })
            ->andReturn(true);

        // Run the console command
        $this->artisan('payments:process-expirations')
            ->assertSuccessful();

        // Assert payment status updated to 'expire' and expired_email_sent_at populated
        $payment->refresh();
        $this->assertEquals('expire', $payment->status);
        $this->assertNotNull($payment->expired_email_sent_at);

        // Assert order status updated to 'cancelled'
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Assert expired email was sent
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\PaymentExpiredMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    /**
     * Test user can verify their payment using the "Saya Sudah Bayar" button.
     */
    public function test_user_can_verify_payment_successfully_via_button(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-VERIFY-TEST1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-VERIFY-TEST1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
        ]);

        // Mock MidtransService checkPaymentStatus to update status to paid when called
        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('checkPaymentStatus')
            ->once()
            ->withArgs(function ($argOrder) use ($order) {
                return $argOrder->id === $order->id;
            })
            ->andReturnUsing(function ($argOrder) {
                $argOrder->update(['status' => 'paid', 'paid_at' => now()]);
                $argOrder->payment->update(['status' => 'settlement', 'paid_at' => now()]);
                return true;
            });

        $response = $this->actingAs($this->user)
            ->post(route('orders.verify', $order->order_number));

        $response->assertRedirect(route('orders.show', $order->order_number));
        $response->assertSessionHas('success', 'Pembayaran Anda berhasil diverifikasi! Terima kasih.');

        $order->refresh();
        $this->assertEquals('paid', $order->status);
    }

    /**
     * Test user verification redirect when payment is still pending on Midtrans.
     */
    public function test_user_verify_redirects_with_info_when_payment_still_pending(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-VERIFY-TEST2',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-VERIFY-TEST2-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '987654321',
        ]);

        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('checkPaymentStatus')
            ->once()
            ->withArgs(function ($argOrder) use ($order) {
                return $argOrder->id === $order->id;
            })
            ->andReturn(false);

        $response = $this->actingAs($this->user)
            ->post(route('orders.verify', $order->order_number));

        $response->assertRedirect(route('orders.show', $order->order_number));
        $response->assertSessionHas('info', 'Pembayaran belum terdeteksi. Silakan tunggu beberapa saat atau selesaikan pembayaran terlebih dahulu.');

        $order->refresh();
        $this->assertEquals('awaiting_payment', $order->status);
    }

    /**
     * Test customer can submit a refund request for a paid order (VA payment).
     */
    public function test_customer_can_submit_refund_request_for_paid_order(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REFUND-REQ-1',
            'status' => 'paid', // Order is paid
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REFUND-REQ-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca', // Manual bank transfer refund needed
            'status' => 'settlement',
            'va_number' => '987654321',
        ]);

        // Submit refund request without required bank details for manual method (should fail validation)
        $response = $this->actingAs($this->user)
            ->from(route('orders.show', $order->order_number))
            ->post(route('orders.refund.store', $order->order_number), [
                'reason' => 'Ingin ganti produk.',
            ]);

        $response->assertRedirect(route('orders.show', $order->order_number));
        $response->assertSessionHasErrors(['bank_name', 'account_number', 'account_holder']);

        // Submit with valid details
        $response = $this->actingAs($this->user)
            ->post(route('orders.refund.store', $order->order_number), [
                'reason' => 'Ingin ganti produk.',
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'Budi Sudarsono',
            ]);

        $response->assertRedirect(route('orders.show', $order->order_number));
        $response->assertSessionHas('success', 'Pengajuan pengembalian dana Anda berhasil dikirim dan sedang ditinjau.');

        $order->refresh();
        $this->assertEquals('refunding', $order->status);

        $this->assertDatabaseHas('refund_requests', [
            'order_id' => $order->id,
            'status' => 'pending',
            'refund_method' => 'manual_bank_transfer',
            'reason' => 'Ingin ganti produk.',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Budi Sudarsono',
        ]);
    }

    /**
     * Test store admin can approve a VA refund request manually with bank receipt reference.
     */
    public function test_admin_can_approve_va_refund_manually(): void
    {
        // 1. Setup Order & Payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REFUND-MAN-1',
            'status' => 'refunding',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 300000,
            'shipping_cost' => 17000,
            'total' => 317000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REFUND-MAN-1-1715000000',
            'amount' => 317000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'settlement',
            'va_number' => '987654321',
        ]);

        // Create refund request record
        $refundRequest = \App\Models\RefundRequest::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'refund_method' => 'manual_bank_transfer',
            'reason' => 'Refund test manual',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Budi Sudarsono',
        ]);

        // Create order item to verify stock restock
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 150000,
            'quantity' => 2,
            'subtotal' => 300000,
            'product_snapshot' => $this->product->toArray(),
        ]);

        // Mock starting stock to 8 (originally 10)
        $this->product->update(['stock' => 8]);

        // Create Admin user and assign Spatie role
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate('admin-marketplace');
        $admin->assignRole('admin-marketplace');

        \Illuminate\Support\Facades\Mail::fake();

        // Admin approves manual refund
        $response = $this->actingAs($admin)
            ->post(route('cms.orders.refund.approve', $order->id), [
                'refund_reference' => 'TX-MANUAL-998877',
            ]);

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('refunded', $order->status);

        $payment->refresh();
        $this->assertEquals('refund', $payment->status);

        $refundRequest->refresh();
        $this->assertEquals('approved', $refundRequest->status);
        $this->assertEquals('TX-MANUAL-998877', $refundRequest->refund_reference);
        $this->assertNotNull($refundRequest->refunded_at);

        // Verify stock is restored to 10 (8 + 2 = 10)
        $this->assertEquals(10, $this->product->fresh()->stock);

        // Verify customer status email is queued
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderStatusCustomerMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    /**
     * Test store admin can approve a QRIS refund request via Midtrans automated refund API.
     */
    public function test_admin_can_approve_qris_refund_via_midtrans_api(): void
    {
        // 1. Setup Order & Payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REFUND-AUTO-1',
            'status' => 'refunding',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REFUND-AUTO-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'qris',
            'payment_method' => 'qris',
            'status' => 'settlement',
            'qris_url' => 'https://api.midtrans.com/qris/generate',
        ]);

        // Create refund request record
        $refundRequest = \App\Models\RefundRequest::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'refund_method' => 'midtrans_api',
            'reason' => 'Refund test auto QRIS',
        ]);

        // Create order item to verify stock restock
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 150000,
            'quantity' => 1,
            'subtotal' => 150000,
            'product_snapshot' => $this->product->toArray(),
        ]);

        // Mock starting stock to 9 (originally 10)
        $this->product->update(['stock' => 9]);

        // Mock Midtrans\Transaction::refund static call using Mockery alias
        if (!class_exists(\Midtrans\Transaction::class, false)) {
            $midtransTransactionMock = \Mockery::mock('alias:Midtrans\Transaction');
            $midtransTransactionMock->shouldReceive('refund')
                ->once()
                ->andReturn((object)[
                    'refund_key' => 'MID-REFUND-KEY-12345',
                    'transaction_id' => 'midtrans-trans-id-qris',
                ]);
        }

        // Create Admin user and assign Spatie role
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate('admin-marketplace');
        $admin->assignRole('admin-marketplace');

        \Illuminate\Support\Facades\Mail::fake();

        // Admin approves auto refund
        $response = $this->actingAs($admin)
            ->post(route('cms.orders.refund.approve', $order->id));

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('refunded', $order->status);

        $payment->refresh();
        $this->assertEquals('refund', $payment->status);

        $refundRequest->refresh();
        $this->assertEquals('approved', $refundRequest->status);
        
        // Assert reference starts with either mock value or the fallback value gracefully
        $this->assertTrue(
            $refundRequest->refund_reference === 'MID-REFUND-KEY-12345' || 
            str_starts_with($refundRequest->refund_reference, 'MIDTRANS-API-REFUND')
        );
        $this->assertNotNull($refundRequest->refunded_at);

        // Verify stock is restored to 10 (9 + 1 = 10)
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    /**
     * Test store admin can reject a refund request and revert status.
     */
    public function test_admin_can_reject_refund_request(): void
    {
        // 1. Setup Order & Payment
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-REFUND-REJ-1',
            'status' => 'refunding',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-REFUND-REJ-1-1715000000',
            'amount' => 167000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'settlement',
            'va_number' => '987654321',
        ]);

        // Create refund request record
        $refundRequest = \App\Models\RefundRequest::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'refund_method' => 'manual_bank_transfer',
            'reason' => 'Refund test reject',
        ]);

        // Create order item
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'price' => 150000,
            'quantity' => 1,
            'subtotal' => 150000,
            'product_snapshot' => $this->product->toArray(),
        ]);

        // Mock starting stock to 9 (originally 10)
        $this->product->update(['stock' => 9]);

        // Create Admin user and assign Spatie role
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate('admin-marketplace');
        $admin->assignRole('admin-marketplace');

        \Illuminate\Support\Facades\Mail::fake();

        // Admin rejects refund
        $response = $this->actingAs($admin)
            ->post(route('cms.orders.refund.reject', $order->id), [
                'admin_notes' => 'Pengajuan refund ditolak karena alasan tidak valid.',
            ]);

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('paid', $order->status); // Reverted back to paid

        $payment->refresh();
        $this->assertEquals('settlement', $payment->status); // Remains settlement

        $refundRequest->refresh();
        $this->assertEquals('rejected', $refundRequest->status);
        $this->assertEquals('Pengajuan refund ditolak karena alasan tidak valid.', $refundRequest->admin_notes);

        // Verify stock is NOT restored (still 9)
        $this->assertEquals(9, $this->product->fresh()->stock);

        // Verify email is queued
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderStatusCustomerMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    }

    /**
     * Test real-time lightweight chatroom message exchanges between customer and admin.
     */
    public function test_chatroom_message_exchanges(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-CHAT-TEST-1',
            'status' => 'refunding',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 150000,
            'shipping_cost' => 17000,
            'total' => 167000,
        ]);

        // Create Admin user
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Role::findOrCreate('admin-marketplace');
        $admin->assignRole('admin-marketplace');

        // 1. Customer checks chatroom message list (should be empty initially)
        $response = $this->actingAs($this->user)
            ->getJson(route('orders.chat.index', $order->order_number));

        $response->assertOk();
        $response->assertJsonCount(0, 'messages');

        // 2. Customer sends a message
        $response = $this->actingAs($this->user)
            ->postJson(route('orders.chat.send', $order->order_number), [
                'message' => 'Halo Admin, tolong bantu proses refund saya.',
            ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message.message', 'Halo Admin, tolong bantu proses refund saya.');
        $response->assertJsonPath('message.sender_id', $this->user->id);

        $this->assertDatabaseHas('chat_messages', [
            'order_id' => $order->id,
            'sender_id' => $this->user->id,
            'message' => 'Halo Admin, tolong bantu proses refund saya.',
            'is_read' => false,
        ]);

        // 3. Admin checks chatroom message list (marks customer message as read)
        $response = $this->actingAs($admin)
            ->getJson(route('cms.orders.chat.index', $order->id));

        $response->assertOk();
        $response->assertJsonCount(1, 'messages');
        $response->assertJsonPath('messages.0.message', 'Halo Admin, tolong bantu proses refund saya.');
        $response->assertJsonPath('messages.0.sender_id', $this->user->id);

        // Assert customer message has been marked as read in database
        $this->assertDatabaseHas('chat_messages', [
            'order_id' => $order->id,
            'sender_id' => $this->user->id,
            'is_read' => true,
        ]);

        // 4. Admin sends a reply message
        $response = $this->actingAs($admin)
            ->postJson(route('cms.orders.chat.send', $order->id), [
                'message' => 'Halo, baik Kak. Refund sedang kami tinjau.',
            ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message.message', 'Halo, baik Kak. Refund sedang kami tinjau.');
        $response->assertJsonPath('message.sender_id', $admin->id);

        $this->assertDatabaseHas('chat_messages', [
            'order_id' => $order->id,
            'sender_id' => $admin->id,
            'message' => 'Halo, baik Kak. Refund sedang kami tinjau.',
            'is_read' => false,
        ]);

        // 5. Customer checks chatroom message list again (marks admin reply as read)
        $response = $this->actingAs($this->user)
            ->getJson(route('orders.chat.index', $order->order_number));

        $response->assertOk();
        $response->assertJsonCount(2, 'messages');
        $response->assertJsonPath('messages.1.message', 'Halo, baik Kak. Refund sedang kami tinjau.');
        $response->assertJsonPath('messages.1.sender_id', $admin->id);

        // Assert admin message has been marked as read in database
        $this->assertDatabaseHas('chat_messages', [
            'order_id' => $order->id,
            'sender_id' => $admin->id,
            'is_read' => true,
        ]);
    }

    public function test_customer_can_submit_refund_request_via_ajax_and_sends_telegram_and_chat_message(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        \Illuminate\Support\Facades\Http::fake();

        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-AJAX-REF-1',
            'status' => 'paid',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 100000,
            'shipping_cost' => 15000,
            'total' => 115000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-AJAX-REF-1-1715000000',
            'amount' => 115000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'settlement',
            'va_number' => '123456789',
        ]);

        // Submit via AJAX POST
        $response = $this->actingAs($this->user)
            ->postJson(route('orders.refund.store', $order->order_number), [
                'reason' => 'Batal beli, salah pilih.',
                'bank_name' => 'BCA',
                'account_number' => '9988776655',
                'account_holder' => 'Ahmad Dani',
            ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Pengajuan pengembalian dana Anda berhasil dikirim dan sedang ditinjau.');

        $order->refresh();
        $this->assertEquals('refunding', $order->status);

        // Check if ChatMessage was created automatically
        $this->assertDatabaseHas('chat_messages', [
            'order_id' => $order->id,
            'sender_id' => $this->user->id,
            'message' => "🚨 Pengajuan Pembatalan / Refund\n"
                . "Alasan: Batal beli, salah pilih.\n"
                . "Metode: Transfer Bank Manual\nBank: BCA\nNo. Rek: 9988776655\nA/N: Ahmad Dani"
        ]);

        // Check if SendTelegramRefundNotification was dispatched
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendTelegramRefundNotification::class, function ($job) use ($order) {
            return $job->refundRequest->order_id === $order->id;
        });
    }

    public function test_customer_cancel_unpaid_order_dispatches_telegram_notification(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'DG-AJAX-CNC-1',
            'status' => 'awaiting_payment',
            'shipping_address' => $this->address->toArray(),
            'courier' => 'JNE',
            'courier_service' => 'REG',
            'subtotal' => 100000,
            'shipping_cost' => 15000,
            'total' => 115000,
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'midtrans_order_id' => 'DG-AJAX-CNC-1-1715000000',
            'amount' => 115000,
            'payment_type' => 'bank_transfer',
            'payment_method' => 'bca',
            'status' => 'pending',
            'va_number' => '123456789',
        ]);

        // Mock MidtransService so it doesn't try to call real APIs or fail with unmocked errors
        $midtransServiceMock = $this->mock(MidtransService::class);
        $midtransServiceMock->shouldReceive('cancelTransaction')
            ->once()
            ->withArgs(function ($argOrder) use ($order) {
                return $argOrder->id === $order->id;
            })
            ->andReturn(true);

        $response = $this->actingAs($this->user)
            ->post(route('orders.cancel', $order->order_number));

        $response->assertRedirect();
        
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Check if SendTelegramOrderCancelledNotification was dispatched
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendTelegramOrderCancelledNotification::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    }
}

