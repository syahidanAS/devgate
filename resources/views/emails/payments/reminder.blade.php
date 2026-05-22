<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Segera Lakukan Pembayaran</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8fafc; }
        .wrapper { width: 100%; background-color: #f8fafc; padding: 20px 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 16px; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
        .header { text-align: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .subtitle { font-size: 14px; color: #64748b; margin-top: 5px; font-weight: 500; }
        .alert-card { background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }
        .alert-title { color: #b45309; font-weight: bold; font-size: 14px; margin: 0; }
        .alert-desc { color: #d97706; font-size: 12px; margin: 2px 0 0 0; }
        .details { background-color: #fafafa; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9; margin-bottom: 25px; }
        .details table { width: 100%; }
        .details td { padding: 8px 0; font-size: 14px; }
        .label { color: #64748b; font-weight: 500; }
        .val { color: #1e293b; font-weight: 700; text-align: right; }
        .payment-box { background-color: #e0e7ff; border: 1px solid #c7d2fe; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 25px; }
        .payment-title { font-size: 11px; text-transform: uppercase; tracking-spacing: 1px; color: #4338ca; font-weight: 700; margin: 0 0 10px 0; }
        .payment-val { font-family: monospace; font-size: 22px; font-weight: 900; color: #1e1b4b; letter-spacing: 2px; }
        .qris-img { width: 180px; height: 180px; margin: 10px auto; display: block; border: 1px solid #e2e8f0; border-radius: 10px; padding: 5px; background: white; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff !important; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; transition: background-color 0.2s; text-align: center; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
        .btn:hover { background-color: #4338ca; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>DevGate</h1>
                <p class="subtitle">Pengingat Batas Waktu Pembayaran</p>
            </div>
            
            <p>Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p>Kami ingin mengingatkan bahwa pesanan Anda <strong>#{{ $order->order_number }}</strong> belum dibayarkan. Mohon segera lakukan pembayaran sebelum instruksi pembayaran ini kedaluwarsa.</p>

            <div class="alert-card">
                <div>
                    <p class="alert-title">⚠️ Segera lakukan pembayaran sebelum VA/QRIS nya kedaluwarsa!</p>
                    <p class="alert-desc">Batas waktu pembayaran Anda berakhir pada: <strong>{{ $order->payment->expired_at->format('d M Y, H:i') }} WIB</strong></p>
                </div>
            </div>

            <div class="payment-box">
                @if($order->payment->payment_method === 'qris')
                    <p class="payment-title">Metode Pembayaran: QRIS</p>
                    <img src="{{ $order->payment->qris_url }}" alt="QRIS Code" class="qris-img">
                    <p style="margin: 10px 0 0 0; font-size: 11px; color: #4338ca;">Scan QR Code di atas menggunakan GoPay, OVO, Dana, LinkAja, atau m-Banking Anda.</p>
                @elseif($order->payment->payment_method === 'mandiri')
                    <p class="payment-title">Metode Pembayaran: Mandiri E-Channel</p>
                    <div style="font-size: 13px; color: #1e1b4b; margin-bottom: 8px;">Kode Biller: <strong style="font-size: 15px; font-family: monospace;">{{ $order->payment->payload['biller_code'] ?? '70012' }}</strong></div>
                    <div class="payment-val">{{ $order->payment->va_number }}</div>
                    <p style="margin: 10px 0 0 0; font-size: 11px; color: #4338ca;">Gunakan kode biller dan kode bayar (bill key) di atas pada menu bayar Multi Payment.</p>
                @else
                    <p class="payment-title">Virtual Account {{ strtoupper($order->payment->payment_method) }}</p>
                    <div class="payment-val">{{ $order->payment->va_number }}</div>
                @endif
            </div>

            <div class="details">
                <h3 style="margin-top: 0; font-size: 15px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">Rincian Tagihan</h3>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="label">Total Biaya</td>
                        <td class="val">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Metode Pembayaran</td>
                        <td class="val">
                            @if($order->payment->payment_method === 'qris')
                                QRIS
                            @elseif($order->payment->payment_method === 'mandiri')
                                Mandiri E-Channel
                            @else
                                {{ strtoupper($order->payment->payment_method) }} Virtual Account
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin-top: 30px; margin-bottom: 10px;">
                <a href="{{ route('orders.show', $order->order_number) }}" class="btn">Lihat Detail Pesanan & Panduan</a>
            </div>

            <div class="footer">
                <p>Email ini dikirim otomatis oleh sistem DevGate. Harap tidak membalas email ini.</p>
                <p>&copy; {{ date('Y') }} DevGate Marketplace. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
