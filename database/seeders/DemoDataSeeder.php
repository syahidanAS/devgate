<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('username', 'iot_author')->first();
        if (!$author) {
            $author = User::first();
        }

        // 1. Article Categories
        $categories = [
            ['name' => 'IoT Specialist', 'color' => '#10b981', 'description' => 'Internet of Things devices, protocols, and smart systems.'],
            ['name' => 'Web Development', 'color' => '#3b82f6', 'description' => 'Modern frontend and backend frameworks, API design.'],
            ['name' => 'Artificial Intelligence', 'color' => '#8b5cf6', 'description' => 'Machine learning, neural networks, edge AI, computer vision.'],
            ['name' => 'Embedded System', 'color' => '#f59e0b', 'description' => 'Microcontroller programming, firmware, electronics.'],
            ['name' => 'Networking', 'color' => '#06b6d4', 'description' => 'Routing, switching, network security, IoT communication.'],
            ['name' => 'Automation', 'color' => '#ef4444', 'description' => 'Industrial automation, smart homes, robotics.'],
        ];

        $articleCategoryInstances = [];
        foreach ($categories as $cat) {
            $articleCategoryInstances[$cat['name']] = ArticleCategory::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'color' => $cat['color'],
                'description' => $cat['description'],
                'is_active' => true,
            ]);
        }

        // 2. Article Tags
        $tags = ['Arduino', 'Raspberry Pi', 'NextJS', 'Python', 'MQTT', 'ESP32', 'Docker', 'Machine Learning', 'PLC', 'RTOS'];
        $tagInstances = [];
        foreach ($tags as $tagName) {
            $tagInstances[$tagName] = ArticleTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }

        // 3. Product Categories
        $prodCategories = [
            ['name' => 'IoT Components', 'description' => 'Bridges, modems, and general IoT hardware.'],
            ['name' => 'Sensors', 'description' => 'Temperature, humidity, motion, and gas sensors.'],
            ['name' => 'ESP32 Boards', 'description' => 'WiFi & Bluetooth enabled microcontrollers.'],
            ['name' => 'Arduino Boards', 'description' => 'Official and clone Arduino development boards.'],
            ['name' => 'Networking Tools', 'description' => 'Cables, routers, and switches for IoT networks.'],
            ['name' => 'AI Hardware', 'description' => 'Edge computing devices, accelerators, cameras.'],
            ['name' => 'Embedded Components', 'description' => 'Resistors, capacitors, transistors, breadboards.'],
        ];

        $prodCategoryInstances = [];
        foreach ($prodCategories as $pCat) {
            $prodCategoryInstances[$pCat['name']] = ProductCategory::create([
                'name' => $pCat['name'],
                'slug' => Str::slug($pCat['name']),
                'description' => $pCat['description'],
                'is_active' => true,
            ]);
        }

        // 4. Products
        $productsData = [
            [
                'name' => 'NodeMCU ESP32 ESP-WROOM-32 Development Board',
                'category' => 'ESP32 Boards',
                'price' => 75000,
                'sale_price' => 69000,
                'stock' => 150,
                'weight' => 20,
                'brand' => 'Espressif',
                'excerpt' => 'Powerful dual-core WiFi & Bluetooth development board.',
                'description' => 'NodeMCU ESP32 is an all-in-one MCU+WiFi+Bluetooth board suitable for smart home applications, sensor networks, and IoT development.',
                'specifications' => [
                    'CPU' => 'Xtensa Dual-Core 32-bit LX6',
                    'Clock Speed' => '240 MHz',
                    'SRAM' => '520 KB',
                    'Flash Memory' => '4 MB',
                    'Connectivity' => 'WiFi 802.11 b/g/n, Bluetooth v4.2 BR/EDR & BLE',
                ],
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'DHT22 AM2302 Temperature & Humidity Sensor',
                'category' => 'Sensors',
                'price' => 45000,
                'sale_price' => null,
                'stock' => 200,
                'weight' => 10,
                'brand' => 'Asair',
                'excerpt' => 'High accuracy digital temperature and humidity sensor.',
                'description' => 'DHT22 is a basic, low-cost digital temperature and humidity sensor. It uses a capacitive humidity sensor and a thermistor to measure the surrounding air.',
                'specifications' => [
                    'Accuracy' => 'Humidity +-2%RH, Temperature +-0.5C',
                    'Range' => 'Humidity 0-100%RH, Temperature -40 to 80C',
                    'Interface' => 'Single-bus digital signal',
                ],
                'status' => 'active',
                'is_featured' => true,
            ],
            [
                'name' => 'Raspberry Pi 4 Model B - 8GB RAM',
                'category' => 'AI Hardware',
                'price' => 1450000,
                'sale_price' => 1399000,
                'stock' => 25,
                'weight' => 100,
                'brand' => 'Raspberry Pi Foundation',
                'excerpt' => 'Desktop-grade single board computer, 8GB LPDDR4.',
                'description' => 'Your tiny, dual-display, desktop computer and robot brain, smart home hub, media center, networked AI core, and much more.',
                'specifications' => [
                    'Processor' => 'Broadcom BCM2711, Quad-core Cortex-A72 (ARM v8) 64-bit SoC @ 1.5GHz',
                    'Memory' => '8GB LPDDR4-3200 SDRAM',
                    'Connectivity' => '2.4 GHz and 5.0 GHz IEEE 802.11ac wireless, Bluetooth 5.0, BLE, Gigabit Ethernet',
                ],
                'status' => 'active',
                'is_featured' => true,
            ],
        ];

        $productInstances = [];
        foreach ($productsData as $pData) {
            $catInstance = $prodCategoryInstances[$pData['category']];
            $productInstances[$pData['name']] = Product::create([
                'user_id' => $author->id,
                'category_id' => $catInstance->id,
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'],
                'stock' => $pData['stock'],
                'weight' => $pData['weight'],
                'brand' => $pData['brand'],
                'excerpt' => $pData['excerpt'],
                'description' => $pData['description'],
                'specifications' => $pData['specifications'],
                'status' => $pData['status'],
                'is_featured' => $pData['is_featured'],
            ]);
        }

        // 5. Articles
        $articlesData = [
            [
                'title' => 'Tutorial ESP32: Membaca Sensor Suhu & Kelembaban DHT22 via MQTT',
                'category' => 'IoT Specialist',
                'tags' => ['ESP32', 'DHT22', 'MQTT', 'Arduino'],
                'excerpt' => 'Panduan lengkap membuat program ESP32 untuk membaca DHT22 dan mengirimkan datanya ke Broker MQTT dengan format JSON.',
                'body' => '<h2>Pendahuluan</h2><p>Internet of Things (IoT) memungkinkan perangkat keras seperti ESP32 berkomunikasi satu sama lain secara wireless. Salah satu protokol yang paling efisien untuk IoT adalah MQTT.</p><h3>Komponen yang Dibutuhkan</h3><ul><li>ESP32 Development Board</li><li>DHT22 Sensor</li><li>Kabel Jumper</li></ul><h3>Skema Koneksi</h3><p>Hubungkan VCC DHT22 ke 3.3V ESP32, GND ke GND, dan Data Pin ke GPIO 23.</p>',
                'status' => 'published',
                'is_featured' => true,
                'related_products' => ['NodeMCU ESP32 ESP-WROOM-32 Development Board', 'DHT22 AM2302 Temperature & Humidity Sensor'],
            ],
            [
                'title' => 'Edge AI: Menjalankan Machine Learning Ringan di Raspberry Pi 4',
                'category' => 'Artificial Intelligence',
                'tags' => ['Raspberry Pi', 'Python', 'Machine Learning'],
                'excerpt' => 'Pelajari cara mengoptimalkan model TensorFlow Lite untuk deteksi objek secara real-time langsung di edge menggunakan Raspberry Pi 4.',
                'body' => '<h2>Kenapa Edge AI?</h2><p>Edge AI membawa komputasi kecerdasan buatan langsung ke perangkat lokal tanpa perlu mengirimkan semua data mentah ke cloud. Ini menghemat bandwidth dan mengurangi latency.</p>',
                'status' => 'published',
                'is_featured' => true,
                'related_products' => ['Raspberry Pi 4 Model B - 8GB RAM'],
            ],
        ];

        foreach ($articlesData as $aData) {
            $catInstance = $articleCategoryInstances[$aData['category']];
            $article = Article::create([
                'user_id' => $author->id,
                'category_id' => $catInstance->id,
                'title' => $aData['title'],
                'slug' => Str::slug($aData['title']),
                'excerpt' => $aData['excerpt'],
                'body' => $aData['body'],
                'status' => $aData['status'],
                'is_featured' => $aData['is_featured'],
                'published_at' => now(),
            ]);

            // Attach tags
            $tagIds = [];
            foreach ($aData['tags'] as $tagName) {
                if (isset($tagInstances[$tagName])) {
                    $tagIds[] = $tagInstances[$tagName]->id;
                }
            }
            $article->tags()->sync($tagIds);

            // Attach related products
            $prodIds = [];
            foreach ($aData['related_products'] as $pName) {
                if (isset($productInstances[$pName])) {
                    $prodIds[$productInstances[$pName]->id] = ['context' => 'used_in_article', 'sort_order' => count($prodIds)];
                }
            }
            $article->relatedProducts()->sync($prodIds);
        }
    }
}
