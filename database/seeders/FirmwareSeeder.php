<?php

namespace Database\Seeders;

use App\Models\FirmwareProject;
use App\Models\FirmwareFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FirmwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Project 1: DevGate Smart Node ESP32
        $p1 = FirmwareProject::create([
            'name' => 'DevGate Smart Node ESP32',
            'description' => 'Firmware node pintar untuk pembacaan sensor DHT22/DHT11, kontrol relay, dan integrasi MQTT broker lokal offline.',
            'device_type' => 'ESP32',
        ]);

        $path1 = 'firmwares/devgate-smart-node-esp32/v1_0_0_node.bin';
        Storage::disk('public')->put($path1, 'dummy_esp32_binary_data_devgate_dht22_relay');

        FirmwareFile::create([
            'firmware_project_id' => $p1->id,
            'version' => 'v1.0.0',
            'file_path' => $path1,
            'flash_offset' => '0x1000',
            'changelog' => "- Rilis perdana firmware IoT.\n- Dukungan pembacaan sensor DHT11/DHT22.\n- Driver relay dengan isolasi optocoupler.\n- Protokol komunikasi MQTT lokal offline.",
            'is_active' => true,
        ]);

        $path1_updated = 'firmwares/devgate-smart-node-esp32/v1_1_0_node.bin';
        Storage::disk('public')->put($path1_updated, 'dummy_esp32_binary_data_devgate_dht22_relay_v1_1_0');

        FirmwareFile::create([
            'firmware_project_id' => $p1->id,
            'version' => 'v1.1.0',
            'file_path' => $path1_updated,
            'flash_offset' => '0x1000',
            'changelog' => "- Peningkatan performa koneksi Wi-Fi.\n- Perbaikan bug overhead parsing JSON.\n- Penambahan indikator LED status berkedip.",
            'is_active' => true,
        ]);

        // 2. Project 2: Tasmota WebPlug ESP8266
        $p2 = FirmwareProject::create([
            'name' => 'Tasmota WebPlug ESP8266',
            'description' => 'Firmware open-source alternatif untuk stopkontak pintar berbasis chip ESP8266/ESP8285.',
            'device_type' => 'ESP8266',
        ]);

        $path2 = 'firmwares/tasmota-webplug-esp8266/tasmota_v12_5_0.bin';
        Storage::disk('public')->put($path2, 'dummy_esp8266_binary_data_tasmota_v12_5');

        FirmwareFile::create([
            'firmware_project_id' => $p2->id,
            'version' => 'v12.5.0',
            'file_path' => $path2,
            'flash_offset' => '0x0',
            'changelog' => "- Upgrade ke platform Arduino ESP8266 Core 3.0.2.\n- Optimasi penulisan memori flash SPIFFS.\n- Integrasi pembaca daya energi kwh real-time.",
            'is_active' => true,
        ]);
    }
}
