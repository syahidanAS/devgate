@extends('layouts.app')

@section('title', 'Web Flasher ESP32 & Arduino — DevGate')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="flasherApp()">
       <!-- Ambient glowing backgrounds for cyber-tech look -->
    <div class="absolute top-[20%] left-[-10%] w-[350px] h-[350px] rounded-full bg-indigo-600/10 blur-[100px] pointer-events-none dark:bg-indigo-900/15"></div>
    <div class="absolute bottom-[30%] right-[-10%] w-[450px] h-[450px] rounded-full bg-amber-500/5 blur-[120px] pointer-events-none dark:bg-amber-500/10"></div>

    <!-- Title Header -->
    <div class="text-center max-w-3xl mx-auto mb-10 space-y-3 relative">
        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-500 to-amber-500 text-white shadow-lg shadow-indigo-500/20 mb-2">
            <i class="fa-solid fa-bolt text-xl animate-pulse"></i>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
            Web Flasher <span class="bg-gradient-to-r from-indigo-600 to-violet-500 bg-clip-text text-transparent dark:from-indigo-400 dark:to-cyan-400">DevGate</span>
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base leading-relaxed">
            Flash binary firmware (.bin) langsung dari browser Anda ke mikrokontroler ESP32, ESP8266, dan lainnya via USB. Aman, cepat, dan tanpa instalasi software tambahan.
        </p>
    </div>

    <!-- Browser Compatibility Error Box -->
    <template x-if="!supported">
        <div class="mb-8 p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 dark:bg-rose-950/20 dark:border-rose-900/50 dark:text-rose-300 flex items-start gap-4 shadow-md max-w-4xl mx-auto">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400">
                <i class="fa-solid fa-triangle-exclamation text-lg animate-bounce"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-bold">Browser Anda Tidak Mendukung Web Serial API</h3>
                <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
                    Fitur flashing di browser membutuhkan dukungan Web Serial API. Gunakan browser desktop berbasis Chromium seperti **Google Chrome, Microsoft Edge, atau Opera** versi terbaru.
                    <br>Firefox, Safari, dan perangkat mobile saat ini tidak didukung karena alasan kebijakan privasi & keamanan browser.
                </p>
            </div>
        </div>
    </template>

    <!-- Main Flasher Tool UI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start relative" x-show="supported">
        
        <!-- Left Panel: Configurations & Flash Control (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Step 1: Select Project -->
            <div class="rounded-3xl border border-slate-200/80 bg-white/70 backdrop-blur-md p-6 shadow-xl dark:border-slate-800/80 dark:bg-slate-900/60 transition-all">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-sm font-bold">
                        1
                    </div>
                    <h2 class="text-sm font-extrabold text-slate-850 dark:text-white uppercase tracking-wider">Pilih Projek & Firmware</h2>
                </div>

                <div class="space-y-4">
                    <!-- Project Dropdown -->
                    <div class="space-y-1.5">
                        <label for="project-select" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Projek Firmware</label>
                        <select 
                            id="project-select" 
                            x-model="selectedProjectId" 
                            @change="onProjectChange()"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">-- Pilih Projek Firmware --</option>
                            <template x-for="p in projects" :key="p.id">
                                <option :value="p.id" x-text="p.name + ' (' + p.device_type + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Version Dropdown -->
                    <div class="space-y-1.5" x-show="selectedProjectId">
                        <label for="version-select" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Versi Firmware</label>
                        <select 
                            id="version-select" 
                            x-model="selectedFileId"
                            @change="onVersionChange()"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">-- Pilih Versi --</option>
                            <template x-for="f in availableFiles" :key="f.id">
                                <option :value="f.id" x-text="f.version + ' (Offset: ' + f.flash_offset + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Version Info / Metadata -->
                <div class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-250/20 dark:border-slate-800/60 space-y-2 text-xs" x-show="activeFile" x-cloak>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Keluarga Chip:</span>
                        <span class="font-bold text-indigo-500 dark:text-indigo-400 font-mono" x-text="activeProject?.device_type"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Offset Flash:</span>
                        <span class="font-bold text-indigo-500 dark:text-indigo-400 font-mono" x-text="activeFile?.flash_offset"></span>
                    </div>
                    <!-- Changelog box -->
                    <div x-show="activeFile?.changelog" class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <span class="text-slate-400 font-bold block mb-1 text-[9px] uppercase tracking-wider text-indigo-500">Catatan Perubahan:</span>
                        <p class="text-[11px] text-slate-600 dark:text-slate-350 leading-relaxed font-sans whitespace-pre-line" x-text="activeFile?.changelog"></p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Connection & Baudrate -->
            <div class="rounded-3xl border border-slate-200/80 bg-white/70 backdrop-blur-md p-6 shadow-xl dark:border-slate-800/80 dark:bg-slate-900/60 transition-all" x-cloak>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-sm font-bold">
                        2
                    </div>
                    <h2 class="text-sm font-extrabold text-slate-850 dark:text-white uppercase tracking-wider">Koneksi Serial</h2>
                </div>

                <div class="space-y-4">
                    <!-- Baudrate Select -->
                    <div class="space-y-1.5">
                        <label for="baudrate-select" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Baud Rate (Speed)</label>
                        <select 
                            id="baudrate-select" 
                            x-model="baudrate"
                            :disabled="connected || !activeFile"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:opacity-55"
                        >
                            <option value="115200">115200 (Recommended/Default)</option>
                            <option value="230400">230400</option>
                            <option value="460800">460800</option>
                            <option value="921600">921600 (Fastest)</option>
                        </select>
                    </div>

                    <!-- Connect Action Button -->
                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="connected ? disconnectDevice() : connectDevice()"
                            :disabled="flashing || !activeFile"
                            :class="connected ? 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/20' : 'bg-indigo-600 hover:bg-indigo-500 shadow-indigo-600/20'"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-xs font-bold text-white shadow-lg transition-all disabled:opacity-55 disabled:cursor-not-allowed"
                        >
                            <i :class="connected ? 'fa-solid fa-plug-circle-xmark' : 'fa-solid fa-plug-circle-bolt'" class="text-sm"></i>
                            <span x-text="connected ? 'Putuskan Perangkat' : 'Hubungkan Perangkat'"></span>
                        </button>
                    </div>

                    <!-- Connection Status Indicator -->
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-450">Status:</span>
                        <div class="flex items-center gap-1.5 font-bold">
                            <span :class="connected ? 'bg-emerald-500 animate-pulse' : 'bg-slate-450'" class="h-2 w-2 rounded-full"></span>
                            <span :class="connected ? 'text-emerald-500' : 'text-slate-500'" x-text="statusMessage"></span>
                        </div>
                    </div>

                    <!-- Warning for selection -->
                    <div x-show="!activeFile" class="p-3 rounded-xl bg-slate-50 border border-slate-200/50 dark:bg-slate-950/40 dark:border-slate-800/80 text-slate-500 text-[10px] text-center font-bold tracking-wide uppercase">
                        <i class="fa-solid fa-info-circle mr-1 text-indigo-500"></i> Pilih projek & versi terlebih dahulu
                    </div>
                </div>
            </div>

            <!-- Step 3: Flash Control -->
            <div class="rounded-3xl border border-slate-200/80 bg-white/70 backdrop-blur-md p-6 shadow-xl dark:border-slate-800/80 dark:bg-slate-900/60 transition-all" x-cloak>
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-sm font-bold">
                        3
                    </div>
                    <h2 class="text-sm font-extrabold text-slate-850 dark:text-white uppercase tracking-wider">Eksekusi Flashing</h2>
                </div>

                <div class="space-y-4">
                    <!-- Erase Flash Checkbox -->
                    <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-amber-500/5 border border-amber-500/15 text-amber-600 dark:text-amber-400 text-xs">
                        <input 
                            type="checkbox" 
                            id="erase-flash" 
                            x-model="eraseAll" 
                            :disabled="flashing || !connected"
                            class="rounded border-slate-300 bg-white text-indigo-600 focus:ring-indigo-500 dark:border-slate-800 dark:bg-slate-950"
                        >
                        <label for="erase-flash" class="font-bold uppercase tracking-wider text-[9px] cursor-pointer">Hapus Seluruh Flash Sebelum Menulis (Erase Flash)</label>
                    </div>

                    <!-- Flash Trigger Button -->
                    <button 
                        type="button" 
                        @click="startFlashing()"
                        :disabled="flashing || !connected"
                        class="w-full inline-flex items-center justify-center gap-2.5 px-4 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-500 text-xs font-bold text-white shadow-lg shadow-indigo-600/15 hover:shadow-indigo-600/30 transition-all disabled:opacity-55 disabled:cursor-not-allowed"
                    >
                        <i x-show="!flashing" class="fa-solid fa-bolt text-sm"></i>
                        <i x-show="flashing" class="fa-solid fa-spinner animate-spin text-sm"></i>
                        <span x-text="flashing ? 'Sedang Flashing...' : 'Mulai Flash Firmware'"></span>
                    </button>

                    <!-- Flashing Progress Bar -->
                    <div x-show="flashing || progress > 0" class="space-y-1.5" x-cloak>
                        <div class="flex justify-between text-[10px] font-mono text-slate-450 font-semibold">
                            <span>PROGRES FLASHING</span>
                            <span class="text-white" x-text="progress + '%'"></span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-slate-950 overflow-hidden border border-slate-200/50 dark:border-slate-800">
                            <div 
                                class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-500 transition-all duration-150"
                                :style="'width: ' + progress + '%'"
                            ></div>
                        </div>
                    </div>

                    <!-- Warning for connection -->
                    <div x-show="!connected" class="p-3 rounded-xl bg-slate-50 border border-slate-200/50 dark:bg-slate-950/40 dark:border-slate-800/80 text-slate-500 text-[10px] text-center font-bold tracking-wide uppercase">
                        <i class="fa-solid fa-info-circle mr-1 text-indigo-500"></i> Hubungkan perangkat serial terlebih dahulu
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Panel: Interactive Terminal Logs (lg:col-span-7) -->
        <div class="lg:col-span-7">
            
            <div class="rounded-3xl border border-slate-800 bg-slate-950 overflow-hidden shadow-2xl flex flex-col h-[560px] transition-all">
                
                <!-- Terminal Header -->
                <div class="px-5 py-3.5 border-b border-slate-900 bg-slate-950/80 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <!-- Red, yellow, green window circles -->
                        <div class="flex gap-1.5 mr-2">
                            <span class="h-3 w-3 rounded-full bg-rose-500/80"></span>
                            <span class="h-3 w-3 rounded-full bg-amber-500/80"></span>
                            <span class="h-3 w-3 rounded-full bg-emerald-500/80"></span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-400">
                            <i class="fa-solid fa-terminal mr-1 text-slate-500"></i> esptool-log.sh
                        </span>
                    </div>

                    <!-- Terminal Controls -->
                    <div class="flex items-center gap-3">
                        <!-- Auto Scroll Toggle -->
                        <button 
                            @click="autoScroll = !autoScroll"
                            :class="autoScroll ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/25' : 'text-slate-500 bg-transparent border-transparent'"
                            class="px-2 py-1 rounded-lg border text-[9px] font-bold uppercase tracking-wider transition-all"
                            title="Toggle Auto Scroll"
                        >
                            Auto Scroll: <span x-text="autoScroll ? 'ON' : 'OFF'"></span>
                        </button>

                        <!-- Clear Logs Button -->
                        <button 
                            @click="logs = []" 
                            class="text-slate-450 hover:text-white transition-colors text-[10px] font-bold"
                            title="Bersihkan Log"
                        >
                            <i class="fa-solid fa-trash-can mr-1"></i> Clear
                        </button>
                    </div>
                </div>

                <!-- Terminal Monitor Body -->
                <div 
                    id="terminal-log"
                    class="flex-grow p-5 font-mono text-[11px] text-emerald-400 leading-relaxed overflow-y-auto space-y-1 bg-slate-950 scrollbar-thin scrollbar-thumb-slate-800"
                >
                    <!-- Welcome / Idle state line -->
                    <div class="text-slate-500">[System] Menunggu koneksi perangkat...</div>
                    
                    <template x-for="(log, idx) in logs" :key="idx">
                        <div class="whitespace-pre-wrap select-text selection:bg-emerald-500 selection:text-slate-900 border-l-2 border-transparent hover:border-emerald-500/30 pl-2">
                            <!-- Custom formatting/colors for logs based on prefix -->
                            <template x-if="log.startsWith('[Error]')">
                                <span class="text-rose-500 font-semibold" x-text="log"></span>
                            </template>
                            <template x-if="log.startsWith('[Success]')">
                                <span class="text-emerald-400 font-bold" x-text="log"></span>
                            </template>
                            <template x-if="log.startsWith('[System]')">
                                <span class="text-cyan-400" x-text="log"></span>
                            </template>
                            <template x-if="!log.startsWith('[Error]') && !log.startsWith('[Success]') && !log.startsWith('[System]')">
                                <span class="text-slate-350" x-text="log"></span>
                            </template>
                        </div>
                    </template>
                    
                    <!-- Blinking Cursor -->
                    <div class="flex items-center gap-1 mt-1 pl-2 text-slate-500" x-show="flashing || connected">
                        <span>$</span>
                        <span class="h-3 w-1.5 bg-emerald-500 animate-pulse"></span>
                    </div>
                </div>

                <!-- Terminal Footer Status -->
                <div class="px-5 py-2 border-t border-slate-900 bg-slate-950 flex items-center justify-between text-[9px] text-slate-650 font-mono">
                    <span x-text="'CHIP: ' + (chipName || 'NOT CONNECTED')"></span>
                    <span x-text="'SPEED: ' + baudrate + ' bps'"></span>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    window.flasherApp = function() {
        return {
            projects: @json($projects),
            selectedProjectId: '',
            selectedFileId: '',
            availableFiles: [],
            baudrate: '115200',
            eraseAll: false,
            connected: false,
            flashing: false,
            progress: 0,
            statusMessage: 'Perangkat belum terhubung.',
            logs: [],
            chipName: '',
            supported: 'serial' in navigator,
            port: null,
            transport: null,
            esploader: null,
            autoScroll: true,

            init() {
                // Initialize default logs
                this.addLog('[System] Web Flasher diinisialisasi.');
                if (!this.supported) {
                    this.addLog('[Error] Web Serial API tidak didukung pada browser ini.');
                } else {
                    this.addLog('[System] Web Serial API terdeteksi. Silakan pilih firmware untuk memulai.');
                }
            },

            onProjectChange() {
                this.selectedFileId = '';
                this.availableFiles = [];
                if (this.selectedProjectId) {
                    const project = this.projects.find(p => p.id == this.selectedProjectId);
                    if (project) {
                        this.availableFiles = project.files || [];
                    }
                }
            },

            onVersionChange() {
                // Version change triggers info update
            },

            get activeProject() {
                return this.projects.find(p => p.id == this.selectedProjectId) || null;
            },

            get activeFile() {
                if (!this.activeProject) return null;
                return this.availableFiles.find(f => f.id == this.selectedFileId) || null;
            },

            addLog(text) {
                if (!text) return;
                
                // Process carriage returns (like \r in esptool python outputs)
                const parts = text.split('\n');
                parts.forEach(part => {
                    if (part.includes('\r')) {
                        const subparts = part.split('\r');
                        // Use the last non-empty subpart to replace/append
                        const lastPart = subparts[subparts.length - 1].trim();
                        if (lastPart) {
                            if (this.logs.length > 0 && (this.logs[this.logs.length - 1].startsWith('Writing') || this.logs[this.logs.length - 1].startsWith('Erase') || this.logs[this.logs.length - 1].startsWith('Compressed'))) {
                                this.logs[this.logs.length - 1] = lastPart;
                            } else {
                                this.logs.push(lastPart);
                            }
                        }
                    } else {
                        const cleanLine = part.trim();
                        if (cleanLine) {
                            this.logs.push(cleanLine);
                        }
                    }
                });

                // Enforce maximum log length to prevent browser lag
                if (this.logs.length > 800) {
                    this.logs = this.logs.slice(this.logs.length - 800);
                }

                // Handle auto scroll
                if (this.autoScroll) {
                    this.$nextTick(() => {
                        const term = document.getElementById('terminal-log');
                        if (term) {
                            term.scrollTop = term.scrollHeight;
                        }
                    });
                }
            },

            async connectDevice() {
                this.logs = [];
                this.chipName = '';
                this.addLog('[System] Membuka dialog pemilih port serial browser...');
                this.statusMessage = 'Menghubungkan...';

                try {
                    // Dynamically import esptool-js modules
                    const esptool = await import('https://unpkg.com/esptool-js@0.6.0/bundle.js');
                    const ESPLoader = esptool.ESPLoader;
                    const Transport = esptool.Transport;

                    // 1. Request port selection from user
                    this.port = await navigator.serial.requestPort();
                    this.addLog('[System] Port serial dipilih oleh pengguna.');
                    
                    // 2. Setup Transport & ESPLoader
                    this.transport = new Transport(this.port, true);
                    
                    const self = this;
                    const terminal = {
                        clean() {
                            self.logs = [];
                        },
                        writeLine(data) {
                            self.addLog(data);
                        },
                        write(data) {
                            self.addLog(data);
                        }
                    };

                    this.esploader = new ESPLoader({
                        transport: this.transport,
                        baudrate: parseInt(this.baudrate),
                        terminal: terminal
                    });

                    this.addLog('[System] Menghubungkan ke mikrokontroler (handshaking)...');
                    this.statusMessage = 'Menghubungkan (Handshake)...';
                    
                    // 3. Connect (Performs handshake and chip identification)
                    this.chipName = await this.esploader.main();
                    this.connected = true;
                    this.statusMessage = `Terhubung ke ${this.chipName}`;
                    this.addLog(`[Success] Berhasil terhubung ke chip: ${this.chipName}`);

                } catch (e) {
                    console.error(e);
                    this.port = null;
                    this.transport = null;
                    this.esploader = null;
                    this.connected = false;
                    this.statusMessage = 'Gagal menghubungkan.';
                    this.addLog(`[Error] Gagal menghubungkan: ${e.message}`);
                }
            },

            async disconnectDevice() {
                this.statusMessage = 'Memutuskan koneksi...';
                this.addLog('[System] Menutup port serial...');
                
                try {
                    if (this.transport) {
                        await this.transport.disconnect();
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.port = null;
                    this.transport = null;
                    this.esploader = null;
                    this.connected = false;
                    this.statusMessage = 'Perangkat terputus.';
                    this.addLog('[System] Port serial berhasil ditutup.');
                }
            },

            async startFlashing() {
                if (!this.activeFile) {
                    alert('Harap pilih file firmware terlebih dahulu.');
                    return;
                }

                if (!this.connected || !this.esploader) {
                    alert('Hubungkan perangkat serial Anda terlebih dahulu.');
                    return;
                }

                this.flashing = true;
                this.progress = 0;
                this.statusMessage = 'Mengunduh berkas firmware dari server...';
                this.addLog(`[System] Mengunduh biner firmware: ${this.activeFile.version}`);

                try {
                    // 1. Fetch the binary file from local server storage
                    const response = await fetch(this.activeFile.file_url);
                    if (!response.ok) {
                        throw new Error(`Gagal mengunduh firmware: HTTP status ${response.status}`);
                    }
                    
                    const buffer = await response.arrayBuffer();
                    const firmwareData = new Uint8Array(buffer);
                    this.addLog(`[System] Biner berhasil diunduh. Ukuran: ${firmwareData.length} bytes.`);
                    
                    // 2. Prepare write options
                    this.statusMessage = 'Menulis biner ke flash...';
                    this.addLog('[System] Menginisialisasi pemrograman flash chip...');

                    const offset = parseInt(this.activeFile.flash_offset || '0x1000', 16);
                    this.addLog(`[System] Alamat penulisan (Offset): ${this.activeFile.flash_offset} (${offset})`);

                    const flashOptions = {
                        fileArray: [
                            {
                                data: firmwareData,
                                address: offset
                            }
                        ],
                        flashMode: 'keep',
                        flashFreq: 'keep',
                        flashSize: 'keep',
                        eraseAll: this.eraseAll,
                        compress: true,
                        reportProgress: (fileIndex, written, total) => {
                            this.progress = Math.round((written / total) * 100);
                            this.statusMessage = `Menulis... ${this.progress}% (${(written / 1024).toFixed(0)} KB / ${(total / 1024).toFixed(0)} KB)`;
                        }
                    };

                    // 3. Write flash
                    await this.esploader.writeFlash(flashOptions);
                    
                    this.progress = 100;
                    this.statusMessage = 'Flashing berhasil diselesaikan!';
                    this.addLog('[Success] Pemrograman flash berhasil 100%!');

                    // 4. Perform Hard Reset
                    this.addLog('[System] Mereset chip perangkat (Hard Reset)...');
                    if (typeof this.esploader.hardReset === 'function') {
                        await this.esploader.hardReset();
                    } else if (typeof this.esploader.after === 'function') {
                        await this.esploader.after('hard_reset');
                    }
                    this.addLog('[Success] Reset selesai. Firmware baru sedang berjalan.');

                } catch (e) {
                    console.error(e);
                    this.statusMessage = 'Proses flashing gagal.';
                    this.addLog(`[Error] Flashing gagal: ${e.message}`);
                } finally {
                    this.flashing = false;
                }
            }
        };
    };
</script>
@endsection
