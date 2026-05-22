<?php

/**
 * Tutorial Videos Configuration
 *
 * Manage your YouTube and TikTok tutorial videos here.
 * To add a new video, copy one of the entries below and fill in the details.
 *
 * YouTube video_id: the part after "watch?v=" in the URL
 *   e.g. https://www.youtube.com/watch?v=dQw4w9WgXcQ → video_id: "dQw4w9WgXcQ"
 *
 * TikTok video_id: the number at the end of the TikTok URL
 *   e.g. https://www.tiktok.com/@user/video/7123456789 → video_id: "7123456789"
 */

return [

    'youtube_channel_url' => env('YOUTUBE_CHANNEL_URL', '#'),
    'tiktok_profile_url'  => env('TIKTOK_PROFILE_URL',  '#'),

    'youtube' => [
        [
            'video_id'    => 'zBqnk8g0u4A',
            'title'       => 'Belajar ESP32 dari Nol — Setup & Hello World',
            'description' => 'Tutorial lengkap memulai IoT dengan ESP32: install Arduino IDE, upload kode pertama, dan blink LED.',
            'duration'    => '12:34',
            'views'       => '18.4K',
        ],
        [
            'video_id'    => 'K7h5B6KUJSA',
            'title'       => 'Sensor DHT22 dengan MQTT & Node-RED Dashboard',
            'description' => 'Baca suhu & kelembaban realtime dari DHT22, kirim ke broker MQTT, dan tampilkan di Node-RED.',
            'duration'    => '21:07',
            'views'       => '9.1K',
        ],
        [
            'video_id'    => 'u2gBJbz6NWU',
            'title'       => 'Laravel 11 REST API — CRUD Produk Lengkap',
            'description' => 'Membangun REST API modern dengan Laravel 11, Sanctum auth, resource transformers, dan Postman testing.',
            'duration'    => '35:22',
            'views'       => '24.3K',
        ],
        [
            'video_id'    => 'sJR1Eo0kgSo',
            'title'       => 'Otomasi Rumah dengan Home Assistant & ESP8266',
            'description' => 'Integrasi Home Assistant dengan relay module via ESP8266 dan kendali lampu dari mana saja.',
            'duration'    => '18:55',
            'views'       => '13.7K',
        ],
    ],

    'tiktok' => [
        [
            'video_id'    => '7380919256498258177',
            'title'       => 'ESP32 vs Arduino — Mana yang Harus Dipilih?',
            'description' => 'Perbandingan cepat ESP32 dan Arduino untuk project IoT kamu.',
            'duration'    => '0:58',
            'views'       => '42K',
        ],
        [
            'video_id'    => '7392847561029384449',
            'title'       => '3 Sensor IoT Wajib Punya untuk Pemula',
            'description' => 'DHT22, HC-SR04, dan PIR — trio sensor terbaik untuk project IoT pertama kamu!',
            'duration'    => '0:45',
            'views'       => '31K',
        ],
        [
            'video_id'    => '7401283746510294785',
            'title'       => 'MQTT dalam 60 Detik — Cara Kerja IoT',
            'description' => 'Penjelasan singkat protokol MQTT yang dipakai di semua perangkat IoT modern.',
            'duration'    => '0:60',
            'views'       => '67K',
        ],
        [
            'video_id'    => '7410392847561023745',
            'title'       => 'Laravel Tip: Query Scope yang Bikin Kode Rapi',
            'description' => 'Gunakan local scope di Laravel untuk query yang lebih bersih dan reusable.',
            'duration'    => '0:52',
            'views'       => '18K',
        ],
    ],

];
