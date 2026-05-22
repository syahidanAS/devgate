<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Marketplace\CartService;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected MidtransService $midtransService;

    public function __construct(CartService $cartService, MidtransService $midtransService)
    {
        $this->cartService = $cartService;
        $this->midtransService = $midtransService;
    }

    /**
     * Display the checkout page.
     */
    public function index()
    {
        $cartItems = $this->cartService->getItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $summary = $this->cartService->getSummary();
        $user = Auth::user();
        $addresses = $user->addresses;
        $defaultAddress = $user->getDefaultAddress();

        // Get provinces from RajaOngkir if API key is set
        $provinces = $this->getProvinces();
        if (empty($provinces)) {
            session()->now('error', 'Gagal memuat daftar provinsi dari RajaOngkir. Harap periksa API Key dan koneksi internet Anda.');
        }

        return view('marketplace.checkout', compact('cartItems', 'summary', 'addresses', 'defaultAddress', 'provinces'));
    }

    /**
     * Store new shipping address via AJAX in checkout flow.
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label'           => 'required|string|max:50',
            'recipient_name'  => 'required|string|max:100',
            'phone'           => 'required|string|max:20',
            'address'         => 'required|string|max:500',
            'province'        => 'required|string', // Format: "ID|Name"
            'city'            => 'required|string', // Format: "ID|Name"
            'postal_code'     => 'required|string|max:10',
            'is_default'      => 'nullable|boolean',
        ]);

        $provinceParts = explode('|', $validated['province']);
        $cityParts = explode('|', $validated['city']);

        $provinceId = $provinceParts[0] ?? null;
        $provinceName = $provinceParts[1] ?? $validated['province'];
        
        $cityId = $cityParts[0] ?? null;
        $cityName = $cityParts[1] ?? $validated['city'];

        // Reset default if this is default
        if ($request->boolean('is_default')) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id'        => Auth::id(),
            'label'          => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone'          => $validated['phone'],
            'address'        => $validated['address'],
            'city'           => $cityName,
            'city_id'        => $cityId,
            'province'       => $provinceName,
            'province_id'    => $provinceId,
            'postal_code'    => $validated['postal_code'],
            'is_default'     => $request->boolean('is_default', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat pengiriman berhasil ditambahkan.',
            'address' => $address,
        ]);
    }

    /**
     * Complete checkout and prepare payment.
     */
    public function process(Request $request)
    {
        $request->validate([
            'address_id'      => 'required|exists:addresses,id',
            'courier'         => 'required|string',
            'courier_service' => 'required|string',
            'shipping_cost'   => 'required|integer|min:0',
            'notes'           => 'nullable|string|max:250',
            'payment_method'  => 'required|in:bca,mandiri,bni,bri,qris',
        ]);

        $user = Auth::user();
        $cartItems = $this->cartService->getItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $address = Address::find($request->input('address_id'));
        $summary = $this->cartService->getSummary();
        
        $shippingCost = $request->integer('shipping_cost');
        $subtotal = $summary['subtotal'];
        $discount = 0; // extendable for voucher discounts later
        $total = $subtotal + $shippingCost - $discount;

        DB::beginTransaction();

        try {
            // Generate unique Order Number: DG-{YYYYMMDD}-{RandomString}
            $orderNumber = 'DG-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => $orderNumber,
                'status'           => 'pending',
                'shipping_address' => [
                    'recipient_name' => $address->recipient_name,
                    'phone'          => $address->phone,
                    'address'        => $address->address,
                    'city'           => $address->city,
                    'province'       => $address->province,
                    'postal_code'    => $address->postal_code,
                ],
                'courier'          => strtoupper($request->input('courier')),
                'courier_service'  => $request->input('courier_service'),
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'discount'         => $discount,
                'total'            => $total,
                'notes'            => $request->input('notes'),
            ]);

            // Save order items & build product snapshots
            foreach ($cartItems as $item) {
                // Stock validation
                $product = $item->product;
                if ($product->track_stock && $product->stock < $item->quantity) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $product->id,
                    'product_name'     => $product->name,
                    'product_sku'      => $product->sku,
                    'price'            => $product->effective_price,
                    'quantity'         => $item->quantity,
                    'subtotal'         => $item->subtotal,
                    'product_snapshot' => $product->toArray(),
                ]);
            }

            // Generate Midtrans transaction using Core API
            $this->midtransService->createTransaction($order, $request->input('payment_method'));

            // Update order status to awaiting payment
            $order->update(['status' => 'awaiting_payment']);

            // Clear Shopping Cart
            $this->cartService->clear();

            DB::commit();

            // Send Telegram notification to admins
            try {
                \App\Jobs\SendTelegramNewOrderNotification::dispatch($order);
            } catch (\Exception $telEx) {
                Log::error('Failed to dispatch telegram order notification: ' . $telEx->getMessage());
            }

            return redirect()->route('orders.show', $order->order_number)
                ->with('success', 'Order berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Processing Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memproses checkout: ' . $e->getMessage());
        }
    }

    /**
     * Fetch RajaOngkir provinces (Komerce API).
     */
    protected function getProvinces(): array
    {
        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
        
        $fallbackProvinces = [
            ['province_id' => '15', 'province' => 'Bali'],
            ['province_id' => '24', 'province' => 'Bangka Belitung'],
            ['province_id' => '11', 'province' => 'Banten'],
            ['province_id' => '6', 'province' => 'Bengkulu'],
            ['province_id' => '19', 'province' => 'DI Yogyakarta'],
            ['province_id' => '10', 'province' => 'DKI Jakarta'],
            ['province_id' => '5', 'province' => 'Jawa Barat'],
            ['province_id' => '12', 'province' => 'Jawa Tengah'],
            ['province_id' => '18', 'province' => 'Jawa Timur'],
            ['province_id' => '28', 'province' => 'Kalimantan Barat'],
            ['province_id' => '3', 'province' => 'Kalimantan Selatan'],
            ['province_id' => '4', 'province' => 'Kalimantan Tengah'],
            ['province_id' => '7', 'province' => 'Kalimantan Timur'],
            ['province_id' => '31', 'province' => 'Kalimantan Utara'],
            ['province_id' => '1', 'province' => 'Nusa Tenggara Barat (NTB)'],
            ['province_id' => '21', 'province' => 'Nusa Tenggara Timur (NTT)'],
            ['province_id' => '25', 'province' => 'Riau'],
            ['province_id' => '8', 'province' => 'Kepulauan Riau'],
            ['province_id' => '33', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '27', 'province' => 'Sulawesi Tengah'],
            ['province_id' => '20', 'province' => 'Sulawesi Tenggara'],
            ['province_id' => '22', 'province' => 'Sulawesi Utara'],
            ['province_id' => '23', 'province' => 'Sumatera Barat'],
            ['province_id' => '26', 'province' => 'Sumatera Selatan'],
            ['province_id' => '16', 'province' => 'Sumatera Utara'],
            ['province_id' => '30', 'province' => 'Lampung'],
            ['province_id' => '13', 'province' => 'Jambi'],
            ['province_id' => '9', 'province' => 'Aceh (NAD)'],
            ['province_id' => '17', 'province' => 'Gorontalo'],
            ['province_id' => '34', 'province' => 'Sulawesi Barat'],
            ['province_id' => '2', 'province' => 'Maluku'],
            ['province_id' => '32', 'province' => 'Maluku Utara'],
            ['province_id' => '14', 'province' => 'Papua'],
            ['province_id' => '29', 'province' => 'Papua Barat'],
        ];

        if (empty($apiKey)) {
            Log::warning('RajaOngkir API Key belum dipasang di .env. Menggunakan data wilayah offline.');
            session()->now('warning', 'API Key RajaOngkir belum dikonfigurasi. Menggunakan data wilayah offline agar checkout tetap bisa digunakan.');
            return $fallbackProvinces;
        }

        // Zero-Lag Bypass if global offline mode is active
        if (\Illuminate\Support\Facades\Cache::has('rajaongkir_offline_mode')) {
            session()->now('warning', 'Sistem menggunakan mode offline hemat waktu (Bypass). Menggunakan data wilayah offline.');
            return $fallbackProvinces;
        }

        $cacheKey = 'rajaongkir_provinces';
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if (!empty($cached)) {
                return $cached;
            }
        }

        try {
            $response = Http::withHeaders(['Key' => $apiKey])
                ->timeout(3)
                ->get($baseUrl . '/destination/province');
            
            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                if (!empty($data)) {
                    $results = [];
                    foreach ($data as $item) {
                        $results[] = [
                            'province_id' => (string) $item['id'],
                            'province'    => \Illuminate\Support\Str::title(strtolower($item['name'])),
                        ];
                    }
                    // Sort by name alphabetically for premium dropdown order
                    usort($results, fn($a, $b) => strcmp($a['province'], $b['province']));

                    \Illuminate\Support\Facades\Cache::put($cacheKey, $results, 86400);
                    return $results;
                }
            } else {
                $errorMsg = $response->json('meta.message') ?? 'Status ' . $response->status();
                Log::error('Komerce Province Fetch Failed: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            Log::error('Komerce Province Fetch Error: ' . $e->getMessage());
        }

        // Trigger 10-minute offline mode on general network error/timeout to save load times
        \Illuminate\Support\Facades\Cache::put('rajaongkir_offline_mode', true, 600);
        session()->now('warning', 'Koneksi RajaOngkir lambat/tidak terjangkau (Offline). Menggunakan data wilayah offline agar checkout tetap bisa digunakan.');
        return $fallbackProvinces;
    }

    /**
     * Fetch RajaOngkir cities under specific province (Komerce API).
     */
    public function getCities(string $provinceId)
    {
        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
        
        $offlineCities = match ($provinceId) {
            '15' => [ // Bali
                ['city_id' => '114', 'city_name' => 'Denpasar', 'type' => '', 'postal_code' => ''],
                ['city_id' => '17', 'city_name' => 'Badung', 'type' => '', 'postal_code' => ''],
                ['city_id' => '128', 'city_name' => 'Gianyar', 'type' => '', 'postal_code' => ''],
            ],
            '11' => [ // Banten
                ['city_id' => '592', 'city_name' => 'Tangerang', 'type' => '', 'postal_code' => ''],
                ['city_id' => '594', 'city_name' => 'Tangerang Selatan', 'type' => '', 'postal_code' => ''],
                ['city_id' => '148', 'city_name' => 'Serang', 'type' => '', 'postal_code' => ''],
                ['city_id' => '143', 'city_name' => 'Cilegon', 'type' => '', 'postal_code' => ''],
            ],
            '19' => [ // Yogyakarta
                ['city_id' => '501', 'city_name' => 'Yogyakarta', 'type' => '', 'postal_code' => ''],
                ['city_id' => '419', 'city_name' => 'Sleman', 'type' => '', 'postal_code' => ''],
                ['city_id' => '39', 'city_name' => 'Bantul', 'type' => '', 'postal_code' => ''],
            ],
            '10' => [ // Jakarta
                ['city_id' => '137', 'city_name' => 'Jakarta Pusat', 'type' => '', 'postal_code' => ''],
                ['city_id' => '135', 'city_name' => 'Jakarta Barat', 'type' => '', 'postal_code' => ''],
                ['city_id' => '136', 'city_name' => 'Jakarta Selatan', 'type' => '', 'postal_code' => ''],
                ['city_id' => '138', 'city_name' => 'Jakarta Utara', 'type' => '', 'postal_code' => ''],
                ['city_id' => '139', 'city_name' => 'Jakarta Timur', 'type' => '', 'postal_code' => ''],
            ],
            '5' => [ // Jawa Barat
                ['city_id' => '23', 'city_name' => 'Bandung', 'type' => '', 'postal_code' => ''],
                ['city_id' => '54', 'city_name' => 'Bekasi', 'type' => '', 'postal_code' => ''],
                ['city_id' => '78', 'city_name' => 'Bogor', 'type' => '', 'postal_code' => ''],
                ['city_id' => '115', 'city_name' => 'Depok', 'type' => '', 'postal_code' => ''],
            ],
            '12' => [ // Jawa Tengah
                ['city_id' => '399', 'city_name' => 'Semarang', 'type' => '', 'postal_code' => ''],
                ['city_id' => '445', 'city_name' => 'Surakarta (Solo)', 'type' => '', 'postal_code' => ''],
            ],
            '18' => [ // Jawa Timur
                ['city_id' => '256', 'city_name' => 'Malang', 'type' => '', 'postal_code' => ''],
                ['city_id' => '444', 'city_name' => 'Surabaya', 'type' => '', 'postal_code' => ''],
            ],
            default => [
                ['city_id' => '137', 'city_name' => 'Jakarta Pusat (Fallback)', 'type' => '', 'postal_code' => ''],
            ],
        };

        if (empty($apiKey)) {
            return response()->json([
                'success' => true,
                'results' => $offlineCities,
                'is_offline' => true,
                'message' => 'API Key belum dikonfigurasi. Menggunakan data kota offline.',
            ]);
        }

        // Zero-Lag Bypass if global offline mode is active
        if (\Illuminate\Support\Facades\Cache::has('rajaongkir_offline_mode')) {
            return response()->json([
                'success' => true,
                'results' => $offlineCities,
                'is_offline' => true,
                'message' => 'Mode offline hemat waktu aktif. Menggunakan data kota offline.',
            ]);
        }

        $cacheKey = 'rajaongkir_cities_' . $provinceId;
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if (!empty($cached)) {
                return response()->json([
                    'success' => true,
                    'results' => $cached,
                ]);
            }
        }

        try {
            $response = Http::withHeaders(['Key' => $apiKey])
                ->timeout(3)
                ->get($baseUrl . '/destination/city/' . $provinceId);
            
            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                if (!empty($data)) {
                    $results = [];
                    foreach ($data as $item) {
                        $results[] = [
                            'city_id'     => (string) $item['id'],
                            'city_name'   => \Illuminate\Support\Str::title(strtolower($item['name'])),
                            'type'        => '',
                            'postal_code' => '',
                        ];
                    }
                    // Sort alphabetically for clean order
                    usort($results, fn($a, $b) => strcmp($a['city_name'], $b['city_name']));

                    \Illuminate\Support\Facades\Cache::put($cacheKey, $results, 86400);
                    return response()->json([
                        'success' => true,
                        'results' => $results,
                    ]);
                }
            }
            
            $errorMsg = $response->json('meta.message') ?? 'Status ' . $response->status();
            Log::error('Komerce City Fetch Failed: ' . $errorMsg);
        } catch (\Exception $e) {
            Log::error('Komerce City Fetch Error: ' . $e->getMessage());
        }

        // Trigger 10-minute offline mode on failure to save load times
        \Illuminate\Support\Facades\Cache::put('rajaongkir_offline_mode', true, 600);
        return response()->json([
            'success' => true,
            'results' => $offlineCities,
            'is_offline' => true,
            'message' => 'Koneksi RajaOngkir lambat (Offline). Menggunakan data kota offline.',
        ]);
    }

    /**
     * Fetch shipping costs dynamically via RajaOngkir API (Komerce API).
     */
    public function checkOngkir(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'courier'    => 'required|in:jne,jnt',
        ]);

        $address = Address::find($request->input('address_id'));
        $summary = $this->cartService->getSummary();
        $weight = max(100, $summary['total_weight'] ?? 1000);
        $courier = strtolower($request->input('courier'));

        $apiKey = config('services.rajaongkir.key');
        $baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API Key RajaOngkir belum dipasang di .env.',
            ], 400);
        }

        // If global offline mode is active, bypass immediately
        if (\Illuminate\Support\Facades\Cache::has('rajaongkir_offline_mode')) {
            return $this->getOfflineOngkirResponse($courier, $weight);
        }

        try {
            // Note: Komerce API requires x-www-form-urlencoded format
            $response = Http::asForm()
                ->withHeaders(['Key' => $apiKey])
                ->timeout(3)
                ->post($baseUrl . '/calculate/domestic-cost', [
                    'origin'      => config('services.rajaongkir.origin', '137'), // Dynamic origin warehouse city
                    'destination' => $address->city_id,
                    'weight'      => $weight,
                    'courier'     => $courier,
                ]);

            if ($response->successful()) {
                $results = $response->json('data') ?? [];
                if (!empty($results)) {
                    $services = [];
                    foreach ($results as $res) {
                        $services[] = [
                            'service'     => $res['service'],
                            'description' => $res['description'] ?? $res['service'],
                            'cost'        => (int) ($res['cost'] ?? 0),
                            'etd'         => !empty($res['etd']) ? $res['etd'] : 'N/A',
                        ];
                    }
                    
                    $courierName = $courier === 'jnt' ? 'J&T' : strtoupper($courier);
                    return response()->json([
                        'success' => true,
                        'courier' => $courierName,
                        'services' => $services
                    ]);
                }
            } else {
                $errorMsg = $response->json('meta.message') ?? 'Status ' . $response->status();
                Log::error('Komerce Cost Calculation Failed: ' . $errorMsg);
            }
        } catch (\Exception $e) {
            Log::error('Komerce Cost Calculation Error: ' . $e->getMessage());
            // General connection error triggers offline mode
            \Illuminate\Support\Facades\Cache::put('rajaongkir_offline_mode', true, 600);
        }

        // Fallback to offline cost estimation
        return $this->getOfflineOngkirResponse($courier, $weight);
    }

    /**
     * Generate offline fallback shipping cost estimation.
     */
    protected function getOfflineOngkirResponse(string $courier, int $weight)
    {
        $baseCost = match ($courier) {
            'jne' => 17000,
            'jnt' => 15000,
            default => 15000,
        };

        // Increase cost based on weight (e.g. price per kg)
        $weightKg = ceil($weight / 1000);
        $totalCost = $baseCost * $weightKg;

        $services = [];
        if ($courier === 'jnt') {
            $services[] = [
                'service' => 'EZ',
                'description' => 'Layanan Reguler J&T (Offline Fallback)',
                'cost' => $totalCost,
                'etd' => '2-3 Hari',
            ];
            $services[] = [
                'service' => 'Super',
                'description' => 'Layanan Kilat J&T (Offline Fallback)',
                'cost' => $totalCost + 12000,
                'etd' => '1-1 Hari',
            ];
        } else {
            $services[] = [
                'service' => 'REG',
                'description' => 'Layanan Reguler JNE (Offline Fallback)',
                'cost' => $totalCost,
                'etd' => '2-3 Hari',
            ];
            $services[] = [
                'service' => 'YES',
                'description' => 'Layanan Kilat JNE (Offline Fallback)',
                'cost' => $totalCost + 10000,
                'etd' => '1-1 Hari',
            ];
        }

        $courierName = $courier === 'jnt' ? 'J&T' : strtoupper($courier);

        return response()->json([
            'success' => true,
            'courier' => $courierName,
            'services' => $services,
            'is_offline' => true,
            'message' => 'Koneksi RajaOngkir terhambat (Offline). Menggunakan tarif estimasi otomatis.'
        ]);
    }

}
