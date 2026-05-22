<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Waktu Pembayaran Kedaluwarsa</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8fafc; }
        .wrapper { width: 100%; background-color: #f8fafc; padding: 20px 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 16px; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
        .header { text-align: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px; }
        .header h1 { color: #dc2626; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .subtitle { font-size: 14px; color: #64748b; margin-top: 5px; font-weight: 500; }
        .alert-card { background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 15px 20px; margin-bottom: 25px; }
        .alert-title { color: #991b1b; font-weight: bold; font-size: 14px; margin: 0; }
        .alert-desc { color: #b91c1c; font-size: 12px; margin: 2px 0 0 0; }
        .details { background-color: #fafafa; padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9; margin-bottom: 25px; }
        .details table { width: 100%; }
        .details td { padding: 8px 0; font-size: 14px; }
        .label { color: #64748b; font-weight: 500; }
        .val { color: #1e293b; font-weight: 700; text-align: right; }
        .btn { display: inline-block; background-color: #64748b; color: #ffffff !important; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px; transition: background-color 0.2s; text-align: center; }
        .btn:hover { background-color: #475569; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px; }
        .footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>DevGate</h1>
                <p class="subtitle">Batas Waktu Pembayaran Kedaluwarsa</p>
            </div>
            
            <p>Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p>Kami memberitahukan bahwa batas waktu pembayaran untuk pesanan Anda <strong>#{{ $order->order_number }}</strong> telah terlampaui. Oleh karena itu, sistem kami secara otomatis membatalkan pesanan ini.</p>

            <div class="alert-card">
                <p class="alert-title">❌ Pesanan Dibatalkan Otomatis</p>
                <p class="alert-desc">Sesi pembayaran untuk pesanan ini telah kedaluwarsa karena tidak ada pembayaran yang kami terima hingga batas waktu yang ditentukan.</p>
            </div>

            <div class="details">
                <h3 style="margin-top: 0; font-size: 15px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">Rincian Pesanan</h3>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="label">Total Biaya</td>
                        <td class="val" style="color: #94a3b8; text-decoration: line-through;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Pesanan</td>
                        <td class="val" style="color: #dc2626;">Dibatalkan (Expired)</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 13px; color: #64748b; text-align: center;">Jangan khawatir! Anda dapat membuat pesanan baru kapan saja melalui toko kami.</p>

            <div style="text-align: center; margin-top: 30px; margin-bottom: 10px;">
                <a href="{{ route('orders.show', $order->order_number) }}" class="btn">Lihat Detail Pesanan</a>
            </div>

            <div class="footer">
                <p>Email ini dikirim otomatis oleh sistem DevGate. Harap tidak membalas email ini.</p>
                <p>&copy; {{ date('Y') }} DevGate Marketplace. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
