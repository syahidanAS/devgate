<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fafafa; }
        .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #4f46e5; margin: 0; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px; margin: 10px 0; }
        .bg-paid { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .bg-shipped { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .bg-cancelled { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .bg-completed { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .bg-default { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .details { background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #f1f5f9; margin-bottom: 20px; }
        .details strong { color: #1e293b; }
        .tracking-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1; text-align: center; margin: 20px 0; }
        .tracking-number { font-family: monospace; font-size: 18px; font-weight: bold; color: #4f46e5; letter-spacing: 2px; margin-top: 5px; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
    </style>
</head>
<body>
    @php
        $badgeClass = match($order->status) {
            'paid' => 'bg-paid',
            'shipped' => 'bg-shipped',
            'completed' => 'bg-completed',
            'cancelled', 'refunded' => 'bg-cancelled',
            default => 'bg-default'
        };
    @endphp

    <div class="container">
        <div class="header">
            <h1>DevGate</h1>
            <p>Pembaruan Status Pesanan</p>
        </div>
        
        <p>Halo <strong>{{ $order->user->name }}</strong>,</p>
        <p>Status pesanan Anda <strong>#{{ $order->order_number }}</strong> saat ini telah diperbarui menjadi:</p>
        
        <div style="text-align: center;">
            <span class="status-badge {{ $badgeClass }}">
                {{ $order->status_label }}
            </span>
        </div>

        @if($order->status === 'paid')
            <p>Terima kasih! Pembayaran Anda sebesar <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong> telah berhasil kami terima. Pesanan Anda akan segera kami proses untuk pengiriman.</p>
        @elseif($order->status === 'shipped')
            <p>Kabar gembira! Pesanan Anda sedang dalam perjalanan.</p>
            <div class="tracking-box">
                <p style="margin: 0; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: bold;">Kurir: {{ strtoupper($order->courier) }} - {{ $order->courier_service }}</p>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 12px;">Nomor Resi:</p>
                <div class="tracking-number">{{ $order->tracking_number ?? 'Belum tersedia' }}</div>
            </div>
        @elseif($order->status === 'completed')
            <p>Pesanan Anda telah selesai. Terima kasih telah berbelanja komponen hardware & IoT di DevGate!</p>
        @elseif(in_array($order->status, ['cancelled', 'refunded']))
            <p>Mohon maaf, pesanan Anda telah dibatalkan. Jika Anda memiliki pertanyaan, silakan hubungi tim dukungan kami.</p>
        @else
            <p>Kami sedang memproses pesanan Anda tahap demi tahap.</p>
        @endif

        <h3>Ringkasan Pesanan:</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('orders.show', $order->order_number) }}" class="btn">Lacak Pesanan Anda</a>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem DevGate. Harap tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
