<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle payment notification callback webhook from Midtrans.
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        // 1. Verify Midtrans Signature Key for security
        if (!$this->verifySignature($payload)) {
            Log::warning('Midtrans Webhook Signature Verification Failed!', $payload);
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature key',
            ], 403);
        }

        // 2. Process transaction update
        $processed = $this->midtransService->handleNotification($payload);

        if ($processed) {
            return response()->json([
                'success' => true,
                'message' => 'Notification processed successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification processing failed',
        ], 500);
    }

    /**
     * Verify Midtrans SHA512 Signature Hash.
     */
    protected function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        $signatureKey = $payload['signature_key'] ?? '';

        if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signatureKey) || empty($serverKey)) {
            return false;
        }

        $inputString = $orderId . $statusCode . $grossAmount . $serverKey;
        $hash = hash('sha512', $inputString);

        return hash_equals($hash, $signatureKey);
    }
}
