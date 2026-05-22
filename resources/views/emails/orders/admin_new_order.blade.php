<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fafafa; }
        .header { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #4f46e5; margin: 0; }
        .details { background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #f1f5f9; margin-bottom: 20px; }
        .details strong { color: #1e293b; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-weight: 600; color: #475569; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pesanan Baru DevGate</h1>
            <p>Order #{{ $order->order_number }}</p>
        </div>
        
        <p>Halo Admin,</p>
        <p>Ada pesanan baru yang masuk melalui sistem DevGate. Berikut adalah ringkasannya:</p>

        <div class="details">
            <p><strong>Nama Pelanggan:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            <p><strong>Metode Pengiriman:</strong> {{ strtoupper($order->courier) }} - {{ $order->courier_service }}</p>
        </div>

        <h3>Rincian Produk:</h3>
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('cms.orders.show', $order) }}" class="btn">Lihat Detail di CMS</a>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem DevGate.</p>
        </div>
    </div>
</body>
</html>
