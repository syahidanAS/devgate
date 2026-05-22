@extends('layouts.app')

@section('title', 'Buat Topik Baru — DevGate Forum')

@section('styles')
<!-- Quill snow theme CDN -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-toolbar.ql-snow {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background-color: #f8fafc !important;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        padding: 10px 15px;
    }
    .ql-container.ql-snow {
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background-color: rgba(255, 255, 255, 0.5) !important;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        font-family: 'Outfit', sans-serif;
        font-size: 0.875rem;
    }
    .ql-editor {
        min-height: 280px;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8 !important;
        font-style: normal !important;
    }
    .dark .ql-toolbar.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: #0b0f19 !important;
    }
    .dark .ql-toolbar .ql-stroke {
        stroke: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-fill {
        fill: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-picker {
        color: #94a3b8 !important;
    }
    .dark .ql-toolbar .ql-picker-options {
        background-color: #0b0f19 !important;
        border-color: #1e293b !important;
    }
    .dark .ql-container.ql-snow {
        border: 1px solid #1e293b !important;
        background-color: rgba(2, 6, 23, 0.4) !important;
        color: #f8fafc;
    }
</style>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumbs & Back Link --}}
    <div class="mb-6">
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Forum
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Form Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800/80 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-6 sm:p-8 shadow-sm">
                
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/15">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </span>
                        Mulai Topik Diskusi Baru
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Tanyakan kendala pemrograman, IoT, atau bagikan ide menarik Anda kepada komunitas DevGate.
                    </p>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/20 p-4 text-sm text-rose-600 dark:text-rose-400">
                        <div class="flex items-center gap-2 mb-2 font-bold">
                            <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                            Mohon koreksi kesalahan berikut:
                        </div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Creation Form --}}
                <form action="{{ route('forum.store') }}" method="POST" id="forum-create-form" class="space-y-6">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Judul Topik <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-heading absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   placeholder="Pertanyaan spesifik (misal: 'Error membaca sensor DHT22 dengan ESP32 di Laravel')"
                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-950/40 pl-10 pr-4 py-3 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400/80 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-400 dark:text-slate-500">
                            Buat judul sespesifik mungkin agar mudah dipahami pembaca lain. (Min. 10 karakter)
                        </p>
                    </div>

                    {{-- Body --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Detail Pembahasan <span class="text-rose-500">*</span>
                        </label>
                        
                        {{-- Rich editor container --}}
                        <div id="editor"></div>
                        
                        {{-- Hidden body input --}}
                        <input type="hidden" name="body" id="body_input" value="{{ old('body') }}">
                        
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
                            <span class="flex items-center gap-1">
                                <i class="fa-brands fa-markdown text-indigo-500 text-sm"></i> 
                                Gunakan paragraf yang jelas. (Min. 20 karakter)
                            </span>
                            <span id="char-count">0 karakter</span>
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div>
                        <label for="tags" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Tag / Kategori Relevan
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" 
                                   name="tags" 
                                   id="tags" 
                                   value="{{ old('tags') }}" 
                                   placeholder="arduino, dht22, esp32, laravel-api"
                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-950/40 pl-10 pr-4 py-3 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400/80 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-400 dark:text-slate-500">
                            Pisahkan antar tag dengan tanda koma ( , ). Maksimal 5 tag. Gunakan huruf kecil tanpa spasi.
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                        <a href="{{ route('forum.index') }}" 
                           class="rounded-xl border border-slate-200 dark:border-slate-800 bg-transparent px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/40 hover:-translate-y-px active:translate-y-0 transition-all">
                            <i class="fa-regular fa-paper-plane"></i> Publikasikan Topik
                        </button>
                    </div>

                </form>

            </div>

        </div>

        {{-- Guide Column --}}
        <div class="space-y-6">
            
            {{-- Panduan Penulisan --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800/80 bg-white/70 dark:bg-slate-900/50 backdrop-blur-md p-6 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-indigo-500"></i> Panduan Menulis Topik
                </h3>
                <ol class="space-y-4 text-xs text-slate-600 dark:text-slate-400 pl-4 list-decimal">
                    <li class="leading-relaxed">
                        <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Rumuskan masalah dengan jelas</strong>
                        Pastikan judul mencerminkan inti pertanyaan utama agar pembaca langsung paham maksud masalah Anda.
                    </li>
                    <li class="leading-relaxed">
                        <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Sertakan detail teknis</strong>
                        Tuliskan hardware, software, modul, atau framework beserta versinya jika relevan.
                    </li>
                    <li class="leading-relaxed">
                        <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Tempel kode / Error Log</strong>
                        Letakkan potongan kode program yang bermasalah agar lebih mudah dibaca dan didiagnosis oleh pakar.
                    </li>
                    <li class="leading-relaxed">
                        <strong class="text-slate-800 dark:text-slate-200 block mb-0.5">Pilih tag yang tepat</strong>
                        Gunakan tag spesifik seperti <span class="font-mono bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-[10px]">esp32</span> atau <span class="font-mono bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-[10px]">laravel</span> agar pakar di bidang tersebut mudah menemukannya.
                    </li>
                </ol>
            </div>

            {{-- Komunitas DevGate --}}
            <div class="rounded-2xl border border-slate-200/60 dark:border-slate-800/80 bg-gradient-to-br from-indigo-500/10 to-cyan-500/5 dark:from-indigo-950/20 dark:to-cyan-950/10 p-6">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-hands-holding-child text-indigo-500"></i> Budaya Saling Membantu
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Forum DevGate dibangun di atas asas kekeluargaan dan semangat berbagi ilmu teknologi. Berkata sopan, tidak menjatuhkan, dan saling menghargai adalah aturan emas komunitas kami.
                </p>
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')
<!-- Quill JS Library from CDN -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Jelaskan secara detail permasalahan Anda. Sertakan kode program, modul yang dipakai, dan pesan error jika ada...',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'blockquote'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'code-block'],
                    ['clean']
                ]
            }
        });

        // Set initial body value if available (e.g. on validation fallback)
        const oldBodyInput = document.getElementById('body_input');
        if (oldBodyInput && oldBodyInput.value) {
            quill.root.innerHTML = oldBodyInput.value;
        }

        // Live Character Counting
        const charCount = document.getElementById('char-count');
        function updateCharCount() {
            const text = quill.getText().trim();
            const count = text.length;
            charCount.textContent = count + ' karakter';
            
            if (count < 20) {
                charCount.classList.add('text-rose-500');
                charCount.classList.remove('text-slate-400', 'dark:text-slate-500', 'text-emerald-500');
            } else {
                charCount.classList.remove('text-rose-500');
                charCount.classList.add('text-emerald-500');
            }
        }
        quill.on('text-change', updateCharCount);
        updateCharCount(); // Initialize count

        // Sync Quill HTML content to hidden input text field on form submit
        const form = document.getElementById('forum-create-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const editorContent = quill.root.innerHTML;
                
                // Robust check to see if Quill is truly empty
                const isEditorEmpty = quill.getLength() <= 1 || 
                                      editorContent === '<p><br></p>' || 
                                      editorContent === '<p></p>' || 
                                      editorContent === '';
                
                if (isEditorEmpty) {
                    oldBodyInput.value = '';
                } else {
                    oldBodyInput.value = editorContent;
                }
            });
        }
    });
</script>
@endsection
