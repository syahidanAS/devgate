<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\Product;
use App\Models\Thread;
use App\Models\Reply;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DummyContentSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------
        // 1. DUMMY USERS FOR COMMUNITY ALIVENESS
        // ----------------------------------------------------
        $dummyUsersData = [
            [
                'name' => 'Budi Setiawan',
                'username' => 'budi_techno',
                'email' => 'budi@devgate.id',
                'bio' => 'IoT Hobbyist & Embedded Enthusiast. Suka mengulik ESP32 dan Arduino di waktu luang.',
            ],
            [
                'name' => 'Anisa Rahmawati',
                'username' => 'anisa_iot',
                'email' => 'anisa@devgate.id',
                'bio' => 'Hardware Engineer di startup smart agriculture. Fokus pada sensor tanah dan otomasi penyiraman.',
            ],
            [
                'name' => 'Eko Prasetyo',
                'username' => 'eko_embedded',
                'email' => 'eko@devgate.id',
                'bio' => 'Fullstack Web Developer & IoT Integrator. Suka menggabungkan Laravel dengan sensor MQTT.',
            ],
            [
                'name' => 'Riza Fahmi',
                'username' => 'riza_dev',
                'email' => 'riza@devgate.id',
                'bio' => 'Senior Systems Engineer. Berpengalaman dalam protokol industri Modbus, RS485, dan PLC.',
            ],
            [
                'name' => 'Diana Lestari',
                'username' => 'diana_coder',
                'email' => 'diana@devgate.id',
                'bio' => 'Mahasiswi Teknik Elektro yang sedang berjuang dengan skripsi deteksi wajah berbasis AI di Edge.',
            ],
        ];

        $users = [];
        foreach ($dummyUsersData as $uData) {
            $users[] = User::updateOrCreate(
                ['email' => $uData['email']],
                [
                    'name' => $uData['name'],
                    'username' => $uData['username'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'bio' => $uData['bio'],
                ]
            );
        }

        // Get main author or first user
        $mainAuthor = User::where('username', 'iot_author')->first() ?? User::first();
        $adminUser = User::where('username', 'superadmin')->first() ?? User::first();

        // Get or Create Article Categories
        $categories = [
            'IoT Specialist' => '#10b981',
            'Web Development' => '#3b82f6',
            'Artificial Intelligence' => '#8b5cf6',
            'Embedded System' => '#f59e0b',
            'Networking' => '#06b6d4',
            'Automation' => '#ef4444',
        ];

        $categoryInstances = [];
        foreach ($categories as $catName => $color) {
            $categoryInstances[$catName] = ArticleCategory::firstOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'name' => $catName,
                    'color' => $color,
                    'description' => "Kategori bahasan seputar {$catName} di platform DevGate.",
                    'is_active' => true,
                ]
            );
        }

        // Get or Create Tags
        $tagsList = ['Arduino', 'Raspberry Pi', 'NextJS', 'Python', 'MQTT', 'ESP32', 'Docker', 'Machine Learning', 'PLC', 'RTOS', 'LoRa', 'SmartHome', 'PostgreSQL', 'WebSockets', 'Sensors'];
        $tagInstances = [];
        foreach ($tagsList as $tagName) {
            $tagInstances[$tagName] = ArticleTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }

        // Get products to associate if they exist
        $esp32Product = Product::where('name', 'like', '%ESP32%')->first();
        $dht22Product = Product::where('name', 'like', '%DHT22%')->first();
        $rpiProduct = Product::where('name', 'like', '%Raspberry Pi%')->first();

        // ----------------------------------------------------
        // 2. SEED 10 DUMMY ARTICLES (REALISTIC)
        // ----------------------------------------------------
        $articlesData = [
            [
                'title' => 'Mengatasi Overheating & Optimalisasi Thermal pada Raspberry Pi 4',
                'category' => 'Embedded System',
                'tags' => ['Raspberry Pi', 'Python', 'Embedded'],
                'excerpt' => 'Raspberry Pi 4 terkenal memiliki performa tinggi namun cepat panas. Pelajari cara monitoring temperatur SoC dan konfigurasi pendingin aktif/pasif.',
                'body' => '<h2>Latar Belakang Thermal Throttling</h2><p>Raspberry Pi 4 merupakan papan komputer tunggal (SBC) yang sangat tangguh. Namun, dengan frekuensi CPU 1.5GHz bawaan, SoC Broadcom BCM2711 di dalamnya dapat menghasilkan panas yang signifikan saat beban berat. Jika suhu chip mencapai <strong>80°C</strong>, sistem operasi secara otomatis akan menurunkan kecepatan CPU (thermal throttling) untuk melindungi silikon, yang berdampak langsung pada penurunan performa aplikasi Anda.</p>
<h3>1. Cara Memantau Suhu via Terminal</h3>
<p>Anda dapat mengukur temperatur SoC saat ini secara real-time dengan mengetikkan perintah berikut di terminal SSH Anda:</p>
<pre><code>vcgencmd measure_temp</code></pre>
<p>Untuk memantau perubahan temperatur secara berkala setiap 2 detik, gunakan perintah `watch`:</p>
<pre><code>watch -n 2 vcgencmd measure_temp</code></pre>
<h3>2. Solusi Pendinginan Aktif vs Pasif</h3>
<ul>
<li><strong>Heatsink Pasif Aluminium / Tembaga:</strong> Pilihan hening terbaik tanpa suara bising. Cukup untuk beban kerja komputasi ringan hingga menengah.</li>
<li><strong>Kipas Aktif (Fan Shim / Armor Case):</strong> Pilihan mutlak bagi proyek Edge AI, media server 24/7, atau kompilasi kode berat. Kipas yang dialiri daya 5V mampu menjaga suhu SoC tetap di bawah 60°C sekalipun diuji stres penuh.</li>
</ul>
<h3>Kesimpulan</h3>
<p>Dengan manajemen termal yang tepat, Anda dapat mempertahankan performa puncak Raspberry Pi 4 Anda tanpa khawatir degradasi keawetan komponen elektronik jangka panjang.</p>',
                'related_products' => $rpiProduct ? [$rpiProduct->id] : [],
            ],
            [
                'title' => 'Integrasi Aplikasi Mobile Flutter dengan ESP32 via Bluetooth Low Energy (BLE)',
                'category' => 'IoT Specialist',
                'tags' => ['ESP32', 'Arduino'],
                'excerpt' => 'Panduan praktis langkah demi langkah menghubungkan aplikasi mobile Flutter dengan mikrokontroler ESP32 menggunakan protokol Bluetooth Low Energy.',
                'body' => '<h2>Mengapa Menggunakan Bluetooth Low Energy (BLE)?</h2><p>Berbeda dengan Bluetooth klasik yang memakan banyak daya baterai, Bluetooth Low Energy (BLE) dirancang khusus untuk transmisi data hemat energi dalam paket-paket kecil. Ini menjadikannya pilihan ideal untuk menghubungkan smartphone dengan sensor wearable atau perangkat smart home tanpa menguras baterai.</p>
<h3>1. Sisi ESP32: Setup Server BLE</h3>
<p>Menggunakan Arduino IDE dengan modul <code>BLEDevice.h</code>, kita dapat mendeklarasikan Service UUID dan Characteristic UUID untuk menerima atau mengirim data ke aplikasi Flutter:</p>
<pre><code>#include &lt;BLEDevice.h&gt;
#include &lt;BLEUtils.h&gt;
#include &lt;BLEServer.h&gt;

#define SERVICE_UUID        "4fafc201-1fb5-459e-8fcc-c5c9c331914b"
#define CHARACTERISTIC_UUID "beb5483e-36e1-4688-b7f5-ea07361b26a8"

void setup() {
  Serial.begin(115250);
  BLEDevice::init("DevGate-ESP32-BLE");
  BLEServer *pServer = BLEDevice::createServer();
  BLEService *pService = pServer->createService(SERVICE_UUID);
  BLECharacteristic *pCharacteristic = pService->createCharacteristic(
                                         CHARACTERISTIC_UUID,
                                         BLECharacteristic::PROPERTY_READ |
                                         BLECharacteristic::PROPERTY_WRITE
                                       );
  pCharacteristic->setValue("Hello World");
  pService->start();
  pServer->getAdvertising()->start();
  Serial.println("BLE Server siap dideteksi!");
}</code></pre>
<h3>2. Sisi Flutter: Flutter Reactive BLE</h3>
<p>Gunakan package populer seperti <code>flutter_reactive_ble</code> untuk memindai (scan) UUID perangkat ESP32, menghubungkannya, dan melakukan subscribe pada karakteristik sensor guna menerima pembaruan data secara real-time.</p>',
                'related_products' => $esp32Product ? [$esp32Product->id] : [],
            ],
            [
                'title' => 'Membangun Smart Home Gateway Mandiri Menggunakan Home Assistant & Zigbee',
                'category' => 'Automation',
                'tags' => ['SmartHome', 'Raspberry Pi', 'Docker'],
                'excerpt' => 'Singkirkan ketergantungan pada server cloud luar negeri. Pelajari cara memasang Home Assistant secara lokal untuk keamanan dan kecepatan maksimal.',
                'body' => '<h2>Kedaulatan Data Smart Home</h2><p>Sebagian besar perangkat pintar komersial mengharuskan Anda mengirimkan status sakelar dan rekaman sensor Anda ke server cloud milik pihak ketiga (seperti Tuya atau Xiaomi). Jika koneksi internet rumah Anda mati atau server mereka mengalami gangguan, seluruh otomasi rumah Anda akan berhenti berfungsi. Home Assistant memecahkan masalah ini dengan memproses semua data secara **100% lokal** di dalam rumah Anda sendiri.</p>
<h3>1. Instalasi Home Assistant via Docker Compose</h3>
<p>Gunakan Docker Compose untuk menjalankan Home Assistant Container di Raspberry Pi Anda dengan konfigurasi volume persisten:</p>
<pre><code>version: \'3\'
services:
  homeassistant:
    container_name: homeassistant
    image: "ghcr.io/home-assistant/home-assistant:stable"
    volumes:
      - /opt/homeassistant/config:/config
      - /etc/localtime:/etc/localtime:ro
    restart: unless-stopped
    privileged: true
    network_mode: host</code></pre>
<h3>2. Mengapa Memilih Protokol Zigbee?</h3>
<p>Dibandingkan WiFi yang membebani router Anda jika terdapat puluhan perangkat pintar, Zigbee bekerja pada jaringan mesh berdaya sangat rendah. Sensor suhu, sensor gerak, atau sensor pintu Zigbee dapat beroperasi menggunakan baterai kancing kecil selama 1 hingga 2 tahun tanpa perlu diisi ulang.</p>',
                'related_products' => $rpiProduct ? [$rpiProduct->id] : [],
            ],
            [
                'title' => 'Panduan Memulai RTOS (Real-Time Operating System) pada ESP32 menggunakan FreeRTOS',
                'category' => 'Embedded System',
                'tags' => ['ESP32', 'RTOS', 'Arduino'],
                'excerpt' => 'Bosan dengan kode loop() raksasa yang lambat? Saatnya beralih ke FreeRTOS di ESP32 untuk pemrograman multitasking yang sesungguhnya.',
                'body' => '<h2>Apa itu RTOS?</h2><p>Saat Anda membuat program mikrokontroler standar, seluruh logika Anda biasanya berjalan di dalam satu fungsi berulang `void loop()`. Ketika Anda harus membaca sensor, memperbarui layar LCD, dan memproses koneksi WiFi secara bersamaan, fungsi loop ini akan menjadi lambat dan tidak responsif. **Real-Time Operating System (RTOS)** memecah program Anda menjadi beberapa **Task** independen dengan prioritas berbeda yang dijadwalkan oleh kernel operasi secara presisi.</p>
<h3>1. Dual-Core Task Allocation di ESP32</h3>
<p>ESP32 memiliki dua core prosesor (Core 0 dan Core 1). Dengan FreeRTOS, kita bisa menunjuk core spesifik untuk menangani task tertentu agar pemrosesan tidak saling berbenturan:</p>
<pre><code>void setup() {
  Serial.begin(115200);

  // Jalankan pembacaan sensor di Core 0
  xTaskCreatePinnedToCore(
    taskBacaSensor,   /* Fungsi Task */
    "TaskSensor",     /* Nama Task */
    4096,             /* Stack size */
    NULL,             /* Parameter */
    1,                /* Prioritas */
    NULL,             /* Task handle */
    0                 /* Jalankan di Core 0 */
  );

  // Jalankan komunikasi WiFi di Core 1
  xTaskCreatePinnedToCore(
    taskWiFiKoneksi,
    "TaskWiFi",
    8192,
    NULL,
    2,
    NULL,
    1                 /* Jalankan di Core 1 */
  );
}</code></pre>
<h3>2. Penggunaan Semaphores & Queues</h3>
<p>Untuk menghindari kondisi *race condition* (di mana dua core mencoba memodifikasi satu variabel global secara bersamaan), FreeRTOS menyediakan mekanisme **Queue** untuk berkirim pesan antar task secara aman dan **Semaphore** untuk penguncian resource hardware.</p>',
                'related_products' => $esp32Product ? [$esp32Product->id] : [],
            ],
            [
                'title' => 'Implementasi Computer Vision di Edge: Menggunakan OpenCV & ESP32-CAM untuk Deteksi Wajah',
                'category' => 'Artificial Intelligence',
                'tags' => ['ESP32', 'Python', 'Machine Learning'],
                'excerpt' => 'Modul kamera $7 dapat digunakan untuk kecerdasan buatan! Simak arsitektur pemrosesan frame video ESP32-CAM menggunakan server OpenCV lokal.',
                'body' => '<h2>Keterbatasan Hardware ESP32-CAM</h2><p>ESP32-CAM adalah modul mikrokontroler berbasis kamera yang sangat murah. Namun, dengan RAM internal yang terbatas pada 4MB PSRAM, modul ini tidak memiliki cukup daya komputasi untuk menjalankan model kecerdasan buatan berskala besar secara mandiri. Solusi paling efisien adalah menggunakan arsitektur hybrid: **ESP32-CAM bertindak sebagai pengambil gambar & streamer**, sedangkan **server lokal (seperti PC atau Raspberry Pi) bertindak sebagai otak pengolah AI**.</p>
<h3>1. Menyediakan Video Stream HTTP MJPEG di ESP32</h3>
<p>Di Arduino IDE, kita mengunggah contoh sketch `CameraWebServer` bawaan untuk menyiarkan video frame ke jaringan lokal dalam bentuk alamat HTTP MJPEG stream (misalnya `http://192.168.1.50:80/stream`).</p>
<h3>2. Memproses Stream di Python menggunakan OpenCV</h3>
<p>Di komputer server atau Raspberry Pi, kita dapat memproses aliran video tersebut secara real-time untuk mengenali wajah menggunakan OpenCV Cascade Classifier:</p>
<pre><code>import cv2
import urllib.request
import numpy as np

# Load pre-trained face detection cascade
face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + \'haarcascade_frontalface_default.xml\')
stream_url = "http://192.168.1.50:80/stream"
stream = urllib.request.urlopen(stream_url)
bytes_data = b\'\'

while True:
    bytes_data += stream.read(1024)
    a = bytes_data.find(b\'\\xff\\xd8\')
    b = bytes_data.find(b\'\\xff\\xd9\')
    if a != -1 and b != -1:
        jpg = bytes_data[a:b+2]
        bytes_data = bytes_data[b+2:]
        img = cv2.imdecode(np.frombuffer(jpg, dtype=np.uint8), cv2.IMREAD_COLOR)
        
        # Deteksi Wajah
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, 1.1, 4)
        for (x, y, w, h) in faces:
            cv2.rectangle(img, (x, y), (x+w, y+h), (0, 255, 0), 2)
            
        cv2.imshow(\'DevGate Edge AI\', img)
        if cv2.waitKey(1) & 0xFF == ord(\'q\'):
            break</code></pre>',
                'related_products' => $esp32Product ? [$esp32Product->id] : [],
            ],
            [
                'title' => 'Meningkatkan Keamanan Protokol MQTT dengan SSL/TLS pada Mosquitto Broker',
                'category' => 'Networking',
                'tags' => ['MQTT', 'Docker', 'Networking'],
                'excerpt' => 'Data sensor IoT yang ditransmisikan secara polos (plaintext) sangat rentan disadap. Amankan broker Mosquitto Anda menggunakan enkripsi SSL/TLS.',
                'body' => '<h2>Bahaya Plaintext MQTT</h2><p>Secara default, protokol MQTT mengirimkan username, password, dan muatan topik (payload) tanpa enkripsi di port 1883. Siapa pun yang berada dalam jaringan yang sama dapat dengan mudah merekam lalu lintas data tersebut menggunakan alat seperti Wireshark dan mencuri kredensial server Anda. Solusi terbaik adalah membungkus jalur transmisi tersebut dengan enkripsi **SSL/TLS** di port port standar **8883**.</p>
<h3>1. Membuat Self-Signed Certificate untuk Testing</h3>
<p>Untuk kebutuhan internal, kita dapat membuat sertifikat Otoritas Sertifikat (CA) sendiri menggunakan openSSL:</p>
<pre><code># Generate private key untuk CA
openssl genrsa -out ca.key 2048

# Generate sertifikat CA
openssl req -new -x509 -days 3650 -key ca.key -out ca.crt</code></pre>
<h3>2. Konfigurasi Mosquitto Broker</h3>
<p>Letakkan sertifikat server yang telah digenerate ke direktori konfigurasi Mosquitto dan edit berkas `mosquitto.conf` untuk menambahkan baris berikut:</p>
<pre><code>listener 8883
cafile /mosquitto/config/certs/ca.crt
certfile /mosquitto/config/certs/server.crt
keyfile /mosquitto/config/certs/server.key
require_certificate false</code></pre>
<p>Dengan pengaturan ini, semua klien IoT Anda harus memuat file `ca.crt` agar dapat memvalidasi identitas broker sebelum mengirimkan data sensor sensitif.</p>',
                'related_products' => [],
            ],
            [
                'title' => 'Arsitektur Pemrosesan Data Sensor Real-Time Berbasis Node.js, WebSockets, dan InfluxDB',
                'category' => 'Web Development',
                'tags' => ['WebSockets', 'NextJS', 'PostgreSQL'],
                'excerpt' => 'Ketahui cara merancang arsitektur web tangguh yang mampu menangani ratusan metrik data sensor per detik dan merendernya dalam grafik real-time tanpa lag.',
                'body' => '<h2>Tantangan Data Time-Series</h2><p>Database relasional tradisional seperti MySQL atau PostgreSQL seringkali kewalahan jika harus menulis data sensor berkecepatan tinggi secara berulang-ulang setiap milidetik. Database Relasional didesain untuk konsistensi relasi, bukan penulisan aliran data masif. Oleh karena itu, arsitektur modern IoT memisahkan penyimpanan transaksional (User & Tagihan) dengan penyimpanan metrik sensor menggunakan **Time-Series Database (TSDB)** seperti **InfluxDB**.</p>
<h3>1. WebSockets untuk Aliran Data Instan</h3>
<p>Untuk menghindari polling berulang HTTP dari frontend yang memboroskan CPU server, kita menggunakan WebSockets. Ketika gateway Node.js menerima pesan dari perangkat IoT via MQTT, server langsung membroadcast payload tersebut ke browser klien yang sedang aktif menggunakan pustaka Socket.io:</p>
<pre><code>const io = require(\'socket.io\')(server);
const mqtt = require(\'mqtt\');
const client = mqtt.connect(\'mqtt://localhost\');

client.on(\'message\', (topic, message) => {
    const data = JSON.parse(message.toString());
    // Kirim data langsung ke dashboard web browser
    io.emit(\'sensor-update\', data);
    
    // Tulis ke InfluxDB di latar belakang
    writeToInflux(data);
});</code></pre>
<h3>2. Mengapa InfluxDB?</h3>
<p>InfluxDB mengorganisir data dalam format stempel waktu (timestamps) secara native. Struktur ini memungkinkan kueri rentang waktu yang sangat cepat (misalnya: *tampilkan rata-rata suhu sensor ruang server dari jam 08:00 hingga 12:00 kemarin*) dengan penggunaan penyimpanan disk yang sangat optimal berkat teknik kompresi khusus time-series.</p>',
                'related_products' => [],
            ],
            [
                'title' => 'Panduan Desain PCB yang Baik untuk Proyek IoT Frekuensi Tinggi',
                'category' => 'Embedded System',
                'tags' => ['Arduino', 'ESP32', 'Embedded'],
                'excerpt' => 'Merancang PCB frekuensi tinggi (WiFi/BLE) memiliki tantangan tersendiri. Hindari interferensi sinyal dengan mengikuti tips praktis tata letak komponen berikut.',
                'body' => '<h2>Mengapa Tata Letak PCB Sangat Penting?</h2><p>Dalam proyek elektronika dasar yang bekerja di bawah frekuensi rendah, jalur tembaga (trace) hanya berfungsi sebagai penghantar listrik biasa. Namun, begitu Anda bekerja dengan frekuensi tinggi seperti WiFi 2.4GHz di ESP32 atau antena LoRa, trace tersebut mulai berperilaku sebagai komponen transmisif (transmision line). Sinyal dapat memantul, melemah, atau menimbulkan noise elektromagnetik jika desain PCB Anda berantakan.</p>
<h3>1. Penempatan Modul Antena</h3>
<p>Modul antena onboard pada ESP32 (tipe MIFA PCB Antenna) membutuhkan ruang kosong di sekelilingnya agar tidak terhalang.
<ul>
<li><strong>Aturan Emas:</strong> Jangan pernah menaruh komponen elektronik lain, baterai, casing logam, atau trace tembaga di bawah area antena.</li>
<li>Gantungkan ujung antena keluar dari batas papan sirkuit utama jika memungkinkan.</li>
</ul></p>
<h3>2. Ground Plane dan Jalur Return</h3>
<p>Selalu gunakan lapisan bawah PCB dua sisi (2-layer) sebagai lapisan ground yang solid (Copper Pour Ground). Ground plane ini bertindak sebagai perisai terhadap gangguan elektromagnetik (EMI) dan menyediakan jalur terpendek bagi arus kembali (return current) ke sumber daya untuk menghindari terbentuknya ground loop.</p>',
                'related_products' => $esp32Product ? [$esp32Product->id] : [],
            ],
            [
                'title' => 'Mengurangi Konsumsi Daya ESP32 hingga Level Micro-Ampere menggunakan Mode Deep Sleep',
                'category' => 'IoT Specialist',
                'tags' => ['ESP32', 'Sensors', 'Arduino'],
                'excerpt' => 'Ingin proyek IoT Anda bertahan berbulan-bulan dengan baterai LiPo kecil? Pelajari rahasia konfigurasi Deep Sleep di ESP32.',
                'body' => '<h2>Masalah Boros Daya ESP32</h2><p>ESP32 adalah mikrokontroler yang sangat andal, namun keandalan tersebut dibayar dengan konsumsi daya yang cukup besar. Saat WiFi dan Bluetooth menyala aktif secara konstan, ESP32 dapat menyedot arus antara <strong>80mA hingga 240mA</strong>. Pada tingkat konsumsi ini, baterai LiPo berkapasitas 1000mAh akan terkuras habis hanya dalam waktu kurang dari 10 jam. Kuncinya adalah menggunakan **Deep Sleep**.</p>
<h3>1. Memasuki Mode Deep Sleep dengan Timer</h3>
<p>Dalam mode deep sleep, modul WiFi, Bluetooth, dan core CPU utama dimatikan sepenuhnya. Hanya sirkuit RTC (Real-Time Clock) yang tetap menyala untuk menghitung waktu bangun. Arus konsumsi drop drastis hingga sekitar **10-15µA** (micro-Ampere):</p>
<pre><code>#define uS_TO_S_FACTOR 1000000ULL  /* Faktor konversi mikrodetik ke detik */
#define TIME_TO_SLEEP  600        /* ESP32 tidur selama 10 menit */

void setup() {
  Serial.begin(115200);
  
  // Baca sensor disini
  float suhu = bacaSensor();
  kirimDataKeCloud(suhu);

  // Setel timer bangun
  esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);
  Serial.println("ESP32 masuk ke mode tidur sekarang...");
  Serial.flush();
  
  // Masuk ke Deep Sleep
  esp_deep_sleep_start();
}

void loop() {
  // Fungsi loop akan dilewati dan tidak pernah dieksekusi!
}</code></pre>
<h3>2. Wakeup via External Interrupt</h3>
<p>Selain timer, Anda juga bisa membangunkan ESP32 secara instan menggunakan tombol luar (push button) atau sensor gerak (PIR) dengan memanfaatkan RTC GPIO pin eksternal melalui fungsi <code>esp_sleep_enable_ext0_wakeup()</code>.</p>',
                'related_products' => $esp32Product ? [$esp32Product->id, $dht22Product->id] : [],
            ],
            [
                'title' => 'Industrial IoT: Menghubungkan PLC dengan Cloud MQTT via Gateway Node-RED',
                'category' => 'Automation',
                'tags' => ['PLC', 'MQTT', 'Docker'],
                'excerpt' => 'Jembatani teknologi operasional pabrik (OT) dengan teknologi informasi (IT). Cara membaca data Modbus TCP dari PLC dan mengunggahnya ke cloud.',
                'body' => '<h2>Konvergensi OT & IT</h2><p>Dalam otomasi industri konvensional, Programmable Logic Controller (PLC) mengontrol mesin-mesin pabrik secara lokal di dalam jaringan pabrik terisolasi. Namun, untuk kebutuhan analisa efisiensi mesin (OEE) atau prediktif maintenance, data sensor dari PLC harus dapat dikirimkan ke cloud server secara aman. **Node-RED** hadir sebagai jembatan *low-code* tangguh yang menyatukan kedua dunia tersebut.</p>
<h3>1. Membaca Protokol Modbus TCP</h3>
<p>Sebagian besar PLC modern (seperti Siemens S7, Schneider, Omron, atau Delta) mendukung komunikasi standar Modbus TCP via port 502. Menggunakan node <code>node-red-contrib-modbus</code>, kita bisa mendaftarkan alamat IP PLC dan melakukan polling pada register FC3 (Holding Registers) untuk membaca nilai sensor temperatur atau sensor tekanan udara.</p>
<h3>2. Mengubah Data Modbus Menjadi JSON & Mengirim via MQTT</h3>
<p>Setelah data biner dibaca dari register PLC, kita menggunakan fungsi script sederhana di Node-RED untuk mengubah data mentah tersebut menjadi objek JSON yang mudah dibaca, kemudian mengirimkannya ke broker MQTT cloud milik perusahaan menggunakan node `mqtt out` standar.</p>',
                'related_products' => [],
            ],
        ];

        foreach ($articlesData as $aData) {
            $catInstance = $categoryInstances[$aData['category']];
            $article = Article::updateOrCreate(
                ['slug' => Str::slug($aData['title'])],
                [
                    'user_id' => $mainAuthor->id,
                    'category_id' => $catInstance->id,
                    'title' => $aData['title'],
                    'excerpt' => $aData['excerpt'],
                    'body' => $aData['body'],
                    'status' => 'published',
                    'is_featured' => true,
                    'published_at' => now(),
                ]
            );

            // Sync tags
            $tagIds = [];
            foreach ($aData['tags'] as $tagName) {
                if (isset($tagInstances[$tagName])) {
                    $tagIds[] = $tagInstances[$tagName]->id;
                }
            }
            $article->tags()->sync($tagIds);

            // Sync related products
            if (!empty($aData['related_products'])) {
                $syncData = [];
                foreach ($aData['related_products'] as $idx => $pId) {
                    $syncData[$pId] = ['context' => 'used_in_article', 'sort_order' => $idx];
                }
                $article->relatedProducts()->sync($syncData);
            }
        }

        // ----------------------------------------------------
        // 3. SEED 20 COMMUNITY TOPICS (THREADS) & REPLIES
        // ----------------------------------------------------
        $threadsData = [
            [
                'title' => '[TANYA] Kenapa ESP32 saya sering restart sendiri saat koneksi WiFi dinyalakan?',
                'body' => '<p>Halo teman-teman DevGate, saya sedang membuat alat monitoring sensor tanah pakai ESP32. Anehnya, mikrokontroler saya sering mengalami restart acak (reset) setiap kali baris kode <code>WiFi.begin()</code> dipanggil. Padahal kalau WiFi dinonaktifkan, pembacaan sensor lancar jaya.</p><p>Apakah ada hubungannya dengan modul regulator daya? Saya mensuplai daya langsung dari port USB laptop.</p>',
                'tags' => ['ESP32', 'WiFi', 'Sensors'],
                'user' => 'budi_techno',
                'views' => 342,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'eko_embedded',
                        'body' => '<p>Halo mas Budi. Besar kemungkinan ini masalah drop tegangan (brownout). Saat modul radio WiFi ESP32 menyala, dia membutuhkan lonjakan arus singkat hingga **250mA**. Port USB laptop atau kabel data microUSB kualitas rendah sering kali tidak kuat menopang lonjakan arus cepat ini sehingga chip mengalami restart otomatis.</p><p>Coba tambahkan kapasitor elco ukuran <strong>100uF - 470uF</strong> di jalur pin 3.3V dan GND sedekat mungkin ke papan ESP32 untuk menampung cadangan daya sementara.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[SHARING] Library Modbus TCP & RTU paling stabil untuk Arduino Mega',
                'body' => '<p>Bagi teman-teman yang sedang mengerjakan proyek otomasi industri atau membaca sensor sensor pabrik berstandar RS485 Modbus menggunakan Arduino Mega, saya sangat merekomendasikan pustaka <strong>ModbusIP_ESP8266</strong> (yang juga kompatibel dengan Arduino Mega jika ditambah ethernet shield) atau **ModbusRTU** oleh André Sarmento.</p><p>Library ini sangat ringan, tidak memakan banyak memory RAM, dan memiliki penanganan timeout koneksi yang sangat baik dibandingkan library Modbus bawaan lainnya yang sering kali membuat thread program freeze saat PLC offline.</p>',
                'tags' => ['Arduino', 'PLC', 'RTOS'],
                'user' => 'riza_dev',
                'views' => 820,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'eko_embedded',
                        'body' => '<p>Setuju sekali mas Riza! Saya sudah pakai library ini di 3 project otomasi chiller pabrik. Berjalan non-stop berbulan-bulan tanpa ada kendala memory leak. Terima kasih banyak sharingnya!</p>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[SOLVED] Mengatasi Error "Hydration failed because the initial UI does not match" di Next.js 14',
                'body' => '<p>Malam mastah web-dev. Saya baru saja migrasi dashboard sensor IoT saya ke Next.js 14 menggunakan App Router. Namun, saya sering mendapat error aneh di browser console:</p><pre><code>Error: Hydration failed because the initial UI does not match what was rendered on the server.</code></pre><p>Ada yang tahu penyebabnya dan cara memperbaiki error yang mengganggu ini?</p>',
                'tags' => ['NextJS', 'WebSockets', 'Arduino'],
                'user' => 'eko_embedded',
                'views' => 1200,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'superadmin',
                        'body' => '<p>Halo mas Eko. Masalah hidrasi (hydration mismatch) di Next.js biasanya terjadi karena Anda merender komponen dinamis yang nilainya berbeda antara sisi Server (SSR) dan sisi Client (browser).</p><p>Contoh paling klasik dalam dashboard IoT adalah menuliskan fungsi <strong>stempel waktu lokal / Date.now()</strong> atau membaca <strong>localStorage</strong> secara langsung di dalam kode render JSX tanpa menunggu komponen ter-mount.</p><p><strong>Solusi:</strong> Gunakan React hook `useEffect` untuk memicu render komponen dinamis tersebut hanya setelah komponen ter-mount di browser client, atau bungkus komponen bersangkutan menggunakan fitur dynamic import dari Next.js dengan menonaktifkan SSR:</p><pre><code>import dynamic from \'next/dynamic\';\nconst DashboardSensor = dynamic(() => import(\'./DashboardSensor\'), { ssr: false });</code></pre>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[TANYA] Cara setting Mosquitto Broker agar bisa diakses dari jaringan publik (VPS)',
                'body' => '<p>Saya sudah berhasil menginstal broker Mosquitto di VPS Ubuntu saya. Di dalam localhost VPS, pengiriman pesan MQTT lancar. Namun, ketika saya coba menghubungkan ESP32 dari rumah ke alamat IP publik VPS tersebut, koneksinya selalu ditolak (connection refused).</p><p>Apakah ada baris konfigurasi khusus di mosquitto.conf yang harus ditambahkan?</p>',
                'tags' => ['MQTT', 'Docker', 'Networking'],
                'user' => 'anisa_iot',
                'views' => 512,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'riza_dev',
                        'body' => '<p>Halo mbak Anisa. Sejak Mosquitto versi 2.0.x, secara keamanan default mereka **menonaktifkan koneksi luar** dan hanya mendengarkan pada interface lokal (localhost). Untuk membukanya ke publik, Anda harus mendefinisikan listener baru dan mengizinkan akses tanpa autentikasi (jika belum setup username/password).</p><p>Buka berkas `mosquitto.conf` Anda dan tambahkan konfigurasi berikut:</p><pre><code>listener 1883 0.0.0.0\nallow_anonymous true</code></pre><p>Jangan lupa restart service mosquittonya: <code>sudo systemctl restart mosquitto</code>. Pastikan juga firewall VPS (ufw) sudah membuka port 1883.</p>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[DISKUSI] LoRa vs NB-IoT: Mana yang lebih cocok untuk monitoring pertanian di pedesaan?',
                'body' => '<p>Halo kawan-kawan. Saya sedang merancang sistem otomasi pertanian (smart agriculture) berskala luas di area kebun kelapa sawit pedesaan Sumatera yang minim sinyal seluler.</p><p>Saya bingung memilih antara menggunakan modul **LoRa (dengan gateway lokal)** atau **NB-IoT (menggunakan kartu SIM khusus)**. Dari segi biaya operasional, efisiensi daya baterai, dan jangkauan sinyal, mana yang lebih menguntungkan ya?</p>',
                'tags' => ['LoRa', 'Sensors', 'ESP32'],
                'user' => 'anisa_iot',
                'views' => 1540,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'riza_dev',
                        'body' => '<p>Mengingat lokasi proyek di area pedesaan yang minim sinyal seluler, **LoRa** adalah pemenang mutlak di sini. NB-IoT membutuhkan stasiun pemancar seluler (BTS) terdekat yang mendukung frekuensi tersebut. Di pedesaan tengah hutan sawit, sinyal seluler 2G saja sering hilang-timbul, apalagi NB-IoT.</p><p>Dengan LoRa, Anda bisa membangun infrastruktur mandiri: 1 unit gateway LoRa yang terhubung internet via modem satelit/VSAT di kantor kebun, dapat menjangkau node sensor sawit sejauh **3 hingga 10 kilometer** secara mandiri tanpa biaya langganan operator seluler bulanan.</p>',
                        'is_solution' => false,
                    ],
                    [
                        'user' => 'budi_techno',
                        'body' => '<p>Betul kata mas Riza. Secara konsumsi daya juga LoRa jauh lebih hemat karena hanya mengirimkan sinyal radio jarak jauh saat dibutuhkan saja, tidak perlu melakukan handshake jaringan seluler yang memakan waktu lama.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[TANYA] Bagaimana cara kalibrasi sensor pH analog (SEN0161) agar pembacaannya akurat?',
                'body' => '<p>Saya membeli sensor pH air analog DFRobot SEN0161 untuk proyek monitoring hidroponik. Nilai pembacaan voltase analog selalu berubah-ubah dan tidak konsisten dengan pH meter digital yang saya miliki.</p><p>Apakah ada rumus perhitungan kalibrasi matematika yang baku untuk sensor ini di Arduino?</p>',
                'tags' => ['Sensors', 'Arduino', 'ESP32'],
                'user' => 'diana_coder',
                'views' => 210,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'anisa_iot',
                        'body' => '<p>Halo Diana. Sensor pH sangat sensitif terhadap temperatur air dan kestabilan tegangan suplai daya analog. Pastikan suplai daya Arduino Anda stabil di 5.0V (gunakan pin 5V bawaan, jangan suplai dari pin eksternal yang naik turun).</p><p>Untuk kalibrasi, siapkan cairan buffer standar pH 4.0 dan pH 7.0. Catat nilai voltase analog yang dibaca sensor saat dimasukkan ke masing-masing cairan tersebut, lalu gunakan rumus persamaan garis lurus `y = mx + c` untuk menghitung nilai pH berdasarkan gradien voltase pembacaan.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[SHARING] Skema baterai & Solar Panel untuk ESP32 Outdoor yang bertahan bertahun-tahun',
                'body' => '<p>Banyak pemula salah merancang catu daya untuk proyek outdoor, seperti menghubungkan solar panel langsung ke powerbank USB. Hal ini sangat tidak efisien karena powerbank memiliki sirkuit step-up konstan yang mengonsumsi arus diam (quiescent current) yang besar.</p><p><strong>Rekomendasi Skema Terbaik:</strong> Gunakan baterai LiFePO4 3.2V kualitas tinggi + Solar Panel 5V/6V + Modul Charger TP4056 dengan bypass pengaman. Karena tegangan penuh LiFePO4 hanya 3.6V, Anda dapat menghubungkannya langsung ke pin 3.3V ESP32 tanpa regulator eksternal tambahan (LDO) yang membuang daya jadi panas. Jangan lupa gabungkan dengan mode Deep Sleep!</p>',
                'tags' => ['ESP32', 'Sensors', 'Arduino'],
                'user' => 'eko_embedded',
                'views' => 980,
                'is_solved' => false,
                'replies' => []
            ],
            [
                'title' => '[TANYA] Ada yang pernah pakai RTOS di Raspberry Pi? Mohon rekomendasinya',
                'body' => '<p>Halo rekan-rekan. Saya butuh kontrol dengan ketepatan waktu tinggi (deterministic real-time) untuk mengendalikan motor servo robot berkaki banyak, tapi saya juga butuh mengolah gambar kamera OpenCV di Raspberry Pi 4.</p><p>Apakah ada yang pernah mencoba memasang patch **PREEMPT_RT** di Raspberry Pi OS agar berjalan secara real-time?</p>',
                'tags' => ['Raspberry Pi', 'RTOS', 'Embedded'],
                'user' => 'diana_coder',
                'views' => 190,
                'is_solved' => false,
                'replies' => []
            ],
            [
                'title' => '[SOLVED] Cara parsing JSON bersarang (nested) berukuran besar di Arduino tanpa crash',
                'body' => '<p>Saya sedang membuat sistem integrasi API publik cuaca BMKG di Arduino Uno. Payload JSON yang diterima dari API berukuran sekitar 3KB dengan struktur bercabang banyak. Ketika saya mencoba memparsing data tersebut menggunakan library `ArduinoJson`, mikrokontroler saya langsung hang/freeze.</p><p>Apakah ini karena kehabisan memory RAM?</p>',
                'tags' => ['Arduino', 'JSON', 'ESP32'],
                'user' => 'budi_techno',
                'views' => 670,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'eko_embedded',
                        'body' => '<p>Betul mas Budi, RAM Arduino Uno (ATmega328P) itu **sangat kecil**, hanya **2KB**! Jika Anda meload string JSON sebesar 3KB ke memori, sistem akan langsung mengalami buffer overflow/out of memory.</p><p><strong>Solusi:</strong> Gunakan teknik <strong>DynamicJsonDocument</strong> dari ArduinoJson versi 6+ dan batasi pembacaan karakter hanya pada elemen yang Anda butuhkan (filtering JSON). Atau, opsi paling rasional adalah mengganti mikrokontroler ke board dengan kapasitas RAM lebih besar seperti ESP32 (SRAM 520KB) atau STM32.</p>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[TANYA] Mengapa WebSocket server berbasis Laravel Reverb sering disconnect setelah beberapa menit?',
                'body' => '<p>Halo para master. Saya sedang mencoba fitur baru Laravel 11 yaitu Laravel Reverb untuk websocket dashboard real-time IoT saya. Namun, koneksi WebSocket dari klien browser sering kali terputus sendiri secara acak setiap 2-3 menit sekali.</p><p>Adakah rekan di sini yang punya solusi konfigurasi untuk menstabilkan koneksi persistent ini?</p>',
                'tags' => ['WebSockets', 'Laravel', 'NextJS'],
                'user' => 'eko_embedded',
                'views' => 430,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'superadmin',
                        'body' => '<p>Umumnya ini disebabkan oleh setelan **timeout idle** pada Web Server / Reverse Proxy (seperti Nginx atau Cloudflare) yang berada di depan server Laravel Reverb Anda. Nginx akan memutus koneksi jika tidak ada aktivitas transmisi data dalam kurun waktu tertentu.</p><p>Coba tingkatkan parameter `proxy_read_timeout` dan `proxy_send_timeout` di blok konfigurasi Nginx Anda menjadi lebih besar (misalnya 1 hari penuh), atau pastikan opsi **Ping/Keep-alive** di sisi klien diaktifkan setiap 30 detik untuk menandakan bahwa koneksi masih aktif.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[SOLVED] Error "permission denied for schema public" setelah migrasi PostgreSQL di Docker',
                'body' => '<p>Halo, saya baru saja men-deploy aplikasi database PostgreSQL menggunakan Docker Compose di server staging. Saat menjalankan perintah `php artisan migrate`, migrasi terhenti di tengah jalan dengan pesan kesalahan: <code>permission denied for schema public</code>.</p><p>Saya login menggunakan pengguna non-root. Apa perintah SQL untuk memperbaikinya?</p>',
                'tags' => ['Docker', 'PostgreSQL', 'Database'],
                'user' => 'eko_embedded',
                'views' => 880,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'superadmin',
                        'body' => '<p>Itu terjadi karena PostgreSQL versi 15 ke atas mengubah hak akses default skema `public` demi faktor keamanan. Sekarang, pengguna database biasa tidak langsung memiliki izin menulis skema kecuali diberikan secara eksplisit.</p><p>Hubungkan ke PostgreSQL Anda menggunakan pengguna <code>postgres</code> (superuser) dan jalankan perintah hibah hak akses berikut:</p><pre><code>GRANT ALL ON SCHEMA public TO nama_user_database;</code></pre>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[DISKUSI] ESP32-S3 vs ESP32 biasa: Kapan kita benar-benar butuh upgrade ke varian S3?',
                'body' => '<p>Dengan selisih harga berkisar antara Rp 30.000 hingga Rp 50.000 lebih mahal, apa kelebihan utama chip ESP32-S3 dibandingkan dengan ESP32 model lawas? Apakah peningkatan spesifikasi hardware tersebut sebanding dengan kebutuhan proyek IoT standar?</p>',
                'tags' => ['ESP32', 'Hardware', 'Sensors'],
                'user' => 'budi_techno',
                'views' => 1350,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'riza_dev',
                        'body' => '<p>Kelebihan utama ESP32-S3 adalah penambahan modul akselerasi instruksi **vektor AI** terintegrasi, yang mempercepat pemrosesan algoritma neural network kecil secara lokal (Edge AI). Selain itu, S3 memiliki pin GPIO lebih banyak, port USB bawaan (native USB OTG), dan modul pengaman enkripsi hardware tingkat lanjut.</p><p>Jika proyek Anda hanya seputar membaca suhu DHT22 dan mengirimkannya ke MQTT, ESP32 biasa sudah lebih dari cukup. Namun, jika Anda ingin membuat smart camera, voice recognition, atau enkripsi enkripsi data berat, upgrade ke S3 sangat disarankan.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[TANYA] Mengatasi noise pembacaan sensor analog ADC di ESP32 menggunakan filter rata-rata',
                'body' => '<p>Saya membaca sensor kelembaban tanah analog menggunakan pin ADC ESP32. Sinyal yang didapat sangat tidak stabil (banyak noise), nilainya bisa melompat jauh secara acak dari pembacaan sebelumnya.</p><p>Bagaimana cara membuat filter perangkat lunak (software filter) sederhana di Arduino IDE untuk meredam noise pembacaan ini?</p>',
                'tags' => ['ESP32', 'Sensors', 'Arduino'],
                'user' => 'diana_coder',
                'views' => 590,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'anisa_iot',
                        'body' => '<p>Halo Diana. Hal pertama yang perlu diketahui adalah ADC bawaan pada ESP32 memang terkenal kurang linier dan memiliki noise yang cukup tinggi dibandingkan Arduino Uno.</p><p>Sebagai solusi termudah di sisi software, Anda bisa menerapkan teknik **Moving Average Filter** (Filter Rata-Rata Bergerak). Alih-alih langsung menggunakan hasil pembacaan tunggal, lakukan pembacaan sensor sebanyak 20 kali dalam jeda sangat singkat, lalu ambil nilai rata-ratanya:</p><pre><code>float bacaSensorStabil() {\n  long total = 0;\n  int sampel = 20;\n  for(int i=0; i&lt;sampel; i++) {\n    total += analogRead(PIN_SENSOR);\n    delay(5);\n  }\n  return (float)total / sampel;\n}</code></pre>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[SHARING] Cheat sheet perintah-perintah Linux esensial untuk mengelola Raspberry Pi Headless',
                'body' => '<p>Menjalankan Raspberry Pi tanpa monitor & keyboard (headless via SSH) membutuhkan penguasaan terminal Linux. Berikut adalah rangkuman cheatsheet perintah penting yang paling sering saya gunakan sehari-hari:</p><ul><li><code>df -h</code> : Memeriksa sisa kapasitas memori MicroSD.</li><li><code>sudo reboot</code> : Melakukan restart sistem secara aman.</li><li><code>htop</code> : Memantau penggunaan CPU, RAM, dan daftar proses aktif secara interaktif.</li><li><code>sudo systemctl status apache2</code> : Memeriksa status berjalan layanan web server.</li></ul>',
                'tags' => ['Raspberry Pi', 'Linux', 'Headless'],
                'user' => 'riza_dev',
                'views' => 1100,
                'is_solved' => false,
                'replies' => []
            ],
            [
                'title' => '[TANYA] Rekomendasi sensor gas yang sensitif terhadap kebocoran gas LPG dan mudah didapat',
                'body' => '<p>Halo. Saya berencana membuat sistem alarm pendeteksi kebocoran tabung gas LPG dapur rumah terintegrasi buzzer dan kirim notifikasi ke Telegram.</p><p>Kira-kira sensor gas seri MQ apa yang paling sensitif untuk mendeteksi unsur gas Butana/Propana pada LPG?</p>',
                'tags' => ['Sensors', 'Arduino', 'ESP32'],
                'user' => 'budi_techno',
                'views' => 310,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'anisa_iot',
                        'body' => '<p>Gunakan sensor seri **MQ-6** atau **MQ-2**. MQ-6 sangat spesifik dan sensitif terhadap gas LPG, butane, dan propane. Sedangkan MQ-2 bersifat universal (sensitif terhadap asap dan gas mudah terbakar lainnya).</p><p>Sebagai saran tambahan keamanan: Karena gas LPG memiliki berat jenis yang lebih padat daripada udara, gas bocor akan mengalir ke bawah memenuhi lantai ruangan. Jadi, letakkan sensor gas sensor di posisi **bawah** dekat lantai (berkisar 10-30 cm dari lantai), jangan diletakkan di atap langit-langit.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[SOLVED] Cara flashing firmware Tasmota ke stopkontak pintar Sonoff S26',
                'body' => '<p>Saya baru saja membeli Sonoff S26 Smart Plug. Saya ingin menghapus aplikasi bawaan eWeLink dan menggantinya dengan firmware open-source Tasmota agar bisa dikontrol penuh secara offline lokal via MQTT.</p><p>Bagaimana metode penyolderan pin dan flashing firmware-nya?</p>',
                'tags' => ['SmartHome', 'MQTT', 'ESP32'],
                'user' => 'anisa_iot',
                'views' => 720,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'eko_embedded',
                        'body' => '<p>Sonoff S26 menggunakan chip ESP8285 di dalamnya. Anda perlu membongkar casing sekrupnya untuk mengakses pin header pemrograman internal yang bertuliskan: **VCC (3.3V), RX, TX, dan GND**.</p><p><strong>Langkah Flashing:</strong>\n1. Solder kabel jumper sementara ke 4 pin tersebut dan hubungkan ke modul USB-to-TTL FTDI.\n2. Tahan tombol fisik Sonoff saat menghubungkan USB FTDI ke komputer untuk masuk ke mode bootloader flash.\n3. Gunakan program <code>Tasmotizer</code> untuk mendeteksi port COM dan flash berkas firmware `tasmota.bin` dalam hitungan detik.\n4. Selesai! Cabut jumper, tutup casing, stopkontak pintar kini siap diakses via IP browser lokal.</p>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[DISKUSI] Menghadapi era IoT industri (IIoT): Apakah PLC konvensional akan digantikan oleh SBC seperti Raspberry Pi?',
                'body' => '<p>Di berbagai komunitas global sedang hangat diperbincangkan apakah perangkat komputer industri berskala mikro seperti Raspberry Pi dengan sistem operasi real-time dapat menggantikan peran PLC industri konvensional (Siemens/Omron) yang mahal dalam lini kontrol produksi pabrik.</p><p>Bagaimana tanggapan rekan-rekan yang berpengalaman langsung di industri manufaktur?</p>',
                'tags' => ['PLC', 'Raspberry Pi', 'Embedded'],
                'user' => 'riza_dev',
                'views' => 2150,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'superadmin',
                        'body' => '<p>Sebagai praktisi industri, jawaban singkatnya adalah **tidak dalam waktu dekat**. PLC memiliki standar durabilitas tinggi yang disebut industrial-grade (tahan noise listrik masif, kelembaban ekstrem, debu tebal, rentang suhu operasional yang ekstrim, dan sertifikasi proteksi arus pendek built-in) yang tidak dimiliki oleh Raspberry Pi standar konsumen.</p><p>Namun, tren baru yang sedang naik daun adalah **Hybrid IIoT**: Menggunakan PLC untuk mengendalikan proses kritis mesin yang beresiko tinggi secara real-time, lalu memasang SBC Raspberry Pi industri (seperti Kunbus Revolution Pi) di sampingnya murni sebagai **Gateway Data** untuk visualisasi web dan pengiriman statistik ke cloud server.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
            [
                'title' => '[TANYA] Bagaimana cara menggunakan interrupt pada ESP32 untuk mendeteksi push button tanpa debouncing?',
                'body' => '<p>Saya menggunakan Pin Interrupt pada ESP32 untuk mendeteksi tekanan tombol fisik. Namun, setiap kali saya menekan tombol satu kali, fungsi interrupt dipanggil berulang kali (double trigger/bounce).</p><p>Apakah ada trik software sederhana tanpa perlu merangkai sirkuit filter kapasitor tambahan?</p>',
                'tags' => ['ESP32', 'Arduino', 'Sensors'],
                'user' => 'diana_coder',
                'views' => 480,
                'is_solved' => true,
                'replies' => [
                    [
                        'user' => 'eko_embedded',
                        'body' => '<p>Halo Diana. Bounce mekanis terjadi karena plat besi di dalam sakelar tombol memantul-mantul kecil saat ditekan sebelum benar-benar terhubung stabil. Rentang pantulan ini biasanya berlangsung sekitar 10-50 milidetik.</p><p><strong>Trik Software Debouncing:</strong> Catat stempel waktu milidetik terakhir saat interrupt dipicu, dan abaikan trigger berikutnya jika jarak waktunya kurang dari 250 milidetik:</p><pre><code>volatile unsigned long lastTriggerTime = 0;\n\nvoid IRAM_ATTR handleButtonInterrupt() {\n  unsigned long currentTime = millis();\n  if (currentTime - lastTriggerTime &gt; 250) { /* filter 250ms */\n    // Tulis aksi tombol di sini\n    lastTriggerTime = currentTime;\n  }\n}</code></pre>',
                        'is_solution' => true,
                    ]
                ]
            ],
            [
                'title' => '[SHARING] Library UI Dashboard untuk menampilkan grafik data sensor real-time secara cantik',
                'body' => '<p>Jika Anda lelah mendesain grafik web dasboard IoT dari nol, saya sarankan menggunakan perpaduan **Chart.js** (untuk performa render grafik cepat berbasis canvas) dengan stylesheet **Tailwind CSS** untuk layout modular glassmorphism.</p><p>Pilihan alternatif yang lebih interaktif bagi pemula adalah library <strong>ApexCharts</strong> yang memiliki animasi transisi grafik real-time yang sangat mulus saat ada data sensor baru masuk dari koneksi Websocket.</p>',
                'tags' => ['NextJS', 'WebSockets', 'Arduino'],
                'user' => 'eko_embedded',
                'views' => 920,
                'is_solved' => false,
                'replies' => []
            ],
            [
                'title' => '[TANYA] Cara menghubungkan sensor RS485 Modbus ke Arduino Nano menggunakan Modul MAX485',
                'body' => '<p>Saya mempunyai sensor kelembaban tanah industri berbasis RS485 Modbus RTU. Saya ingin membacanya menggunakan Arduino Nano dibantu modul konverter MAX485.</p><p>Bagaimana skema pin pengkabelan dari pin RE & DE MAX485 ke pin GPIO Arduino Nano?</p>',
                'tags' => ['Arduino', 'Sensors', 'PLC'],
                'user' => 'budi_techno',
                'views' => 290,
                'is_solved' => false,
                'replies' => [
                    [
                        'user' => 'riza_dev',
                        'body' => '<p>Halo mas Budi. Modul MAX485 bekerja secara half-duplex (harus bergantian kirim dan terima data). Pin **RE** (Receiver Enable - active low) dan **DE** (Driver Enable - active high) biasanya digabung menjadi satu kabel jumper dan dihubungkan ke salah satu pin digital output Arduino Nano (misalnya D2).</p><p>Saat Anda ingin mengirim instruksi Modbus, setel pin D2 ke level **HIGH**. Setelah selesai mengirim data dan siap mendengarkan jawaban dari sensor, segera setel kembali pin D2 ke level **LOW**.</p>',
                        'is_solution' => false,
                    ]
                ]
            ],
        ];

        foreach ($threadsData as $tData) {
            $userInstance = User::where('username', $tData['user'])->first() ?? $adminUser;
            
            $thread = Thread::create([
                'user_id' => $userInstance->id,
                'title' => $tData['title'],
                'slug' => Thread::generateSlug($tData['title']),
                'body' => $tData['body'],
                'tags' => $tData['tags'],
                'views' => $tData['views'],
                'is_solved' => $tData['is_solved'],
                'is_closed' => false,
                'created_at' => now()->subHours(rand(1, 48)),
            ]);

            // Create replies
            $hasSolution = false;
            foreach ($tData['replies'] as $rData) {
                $replier = User::where('username', $rData['user'])->first() ?? $adminUser;
                
                Reply::create([
                    'thread_id' => $thread->id,
                    'user_id' => $replier->id,
                    'body' => $rData['body'],
                    'is_solution' => $rData['is_solution'],
                    'created_at' => $thread->created_at->addMinutes(rand(10, 120)),
                ]);
            }
        }
    }
}
