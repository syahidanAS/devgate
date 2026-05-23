<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'DevGate — IoT Specialist, Web Dev & Automation')</title>
        @yield('meta')

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- FontAwesome Icons for modern tech vibes -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Swiper.js for premium product/article carousels -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        <!-- Highlight.js for professional tech syntax highlighting -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
            body {
                font-family: 'Outfit', sans-serif;
            }
            code, pre, .font-mono {
                font-family: 'JetBrains Mono', monospace;
            }
        </style>
        @yield('styles')
    </head>
    <body class="antialiased min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">
        <!-- Staging Ribbon Watermark -->
        <div class="fixed top-0 right-0 z-[99999] pointer-events-none select-none overflow-hidden w-40 h-40">
            <div class="absolute top-8 -right-12 w-56 bg-gradient-to-r from-amber-500 via-rose-500 to-red-600 text-white text-[11px] font-black tracking-widest text-center py-1.5 shadow-lg transform rotate-45 border-y border-white/20 uppercase backdrop-blur-sm">
                STAGING
            </div>
        </div>
        
        <!-- Cart Service Injector -->
        @inject('cartService', 'App\Services\Marketplace\CartService')
        @php
            $cartSummary = $cartService->getSummary();
            $cartCount = $cartSummary['total_quantity'] ?? 0;
        @endphp

        <!-- Main Wrapper with dynamic backdrop decorations -->
        <div class="relative overflow-hidden min-h-screen flex flex-col justify-between">
            
            <!-- Tech Ambient Background Lights -->
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-violet-600/10 blur-[120px] pointer-events-none dark:bg-violet-900/20"></div>
            <div class="absolute bottom-[20%] right-[-10%] w-[600px] h-[600px] rounded-full bg-cyan-600/10 blur-[150px] pointer-events-none dark:bg-cyan-950/20"></div>

            <!-- Header / Navbar -->
            <header class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md dark:border-slate-800/80 dark:bg-slate-950/80 transition-colors duration-300">
                <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    
                    <!-- Brand Logo -->
                    <div class="flex items-center gap-6">
                        <a href="/" class="flex items-center gap-2 group">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-600/20 group-hover:scale-105 transition-all">
                                <i class="fa-solid fa-microchip text-lg"></i>
                            </div>
                            <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-600 bg-clip-text text-transparent dark:from-white dark:via-slate-200 dark:to-indigo-400">
                                Dev<span class="text-indigo-600 dark:text-indigo-400 font-extrabold">Gate</span>
                            </span>
                        </a>

                        <!-- Public Navigation Menu (Desktop) -->
                        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300">
                            <a href="{{ route('blog.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors {{ request()->routeIs('blog.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                                <i class="fa-regular fa-newspaper mr-1"></i> Blog & Artikel
                            </a>
                            <a href="{{ route('shop.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors {{ request()->routeIs('shop.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                                <i class="fa-solid fa-store mr-1"></i> Toko IoT & Elektronik
                            </a>
                            <a href="{{ route('forum.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors {{ request()->routeIs('forum.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : '' }}">
                                <i class="fa-solid fa-comments mr-1"></i> Forum Diskusi
                            </a>
                        </nav>
                    </div>

                    <!-- Right Header Utilities -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Dark Mode Toggle Button -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="relative p-2.5 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition-all border border-transparent hover:border-slate-200/50 dark:hover:border-slate-800" aria-label="Toggle Theme">
                            <i class="fa-solid fa-sun text-base scale-0 transition-transform dark:scale-100 absolute left-[11px] top-[11px]"></i>
                            <i class="fa-solid fa-moon text-base dark:scale-0 transition-transform scale-100"></i>
                        </button>

                        @auth
                        <!-- ── Public Notification Bell ── -->
                        <div class="relative"
                             x-data="publicNotificationBell()"
                             x-init="init()"
                             @click.outside="open = false">

                            <!-- Bell Button -->
                            <button @click="open = !open; if(open) fetchNotifications()"
                                    class="relative p-2.5 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 border border-transparent hover:border-slate-200/50 dark:hover:border-slate-800 transition-all"
                                    aria-label="Notifikasi">
                                <i class="fa-regular fa-bell text-base"></i>
                                <!-- Unread badge -->
                                <span x-show="count > 0"
                                      x-text="count > 9 ? '9+' : count"
                                      class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-rose-500 text-[9px] font-extrabold text-white px-1 shadow-lg shadow-rose-500/40 animate-pulse"
                                      x-cloak></span>
                            </button>

                            <!-- Dropdown Panel -->
                            <div x-show="open"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                 class="absolute right-0 top-full mt-3 w-80 rounded-2xl border border-slate-200 bg-white shadow-2xl z-50 overflow-hidden dark:border-slate-800 dark:bg-slate-900 transition-colors duration-300">

                                <!-- Header -->
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-bell text-xs text-indigo-500 dark:text-indigo-400"></i>
                                        <span class="text-xs font-bold text-slate-850 dark:text-white">Notifikasi</span>
                                        <span x-show="count > 0"
                                              x-text="count + ' baru'"
                                              class="inline-flex items-center rounded-full bg-rose-500/10 border border-rose-500/20 px-1.5 py-0.5 text-[9px] font-bold text-rose-600 dark:text-rose-400"
                                              x-cloak></span>
                                    </div>
                                    <button x-show="count > 0"
                                            @click="markAllRead()"
                                            class="text-[10px] font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                            x-cloak>
                                        Tandai Semua Dibaca
                                    </button>
                                </div>

                                <!-- Notification List -->
                                <div class="max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-800">

                                    <!-- Loading state -->
                                    <div x-show="loading" class="flex items-center justify-center py-8 gap-2 text-slate-500 text-xs">
                                        <i class="fa-solid fa-spinner animate-spin text-indigo-500 dark:text-indigo-400"></i>
                                        <span>Memuat...</span>
                                    </div>

                                    <!-- Empty state -->
                                    <div x-show="!loading && notifications.length === 0" class="flex flex-col items-center py-8 text-slate-450 dark:text-slate-500">
                                        <i class="fa-regular fa-bell-slash text-3xl mb-2"></i>
                                        <p class="text-xs font-semibold">Tidak ada notifikasi baru</p>
                                    </div>

                                    <!-- Notifications -->
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <a :href="'/notifications/' + notif.id + '/read'"
                                           class="flex items-start gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-800/40 hover:bg-slate-50 dark:hover:bg-slate-805/40 transition-colors group cursor-pointer">

                                            <!-- Icon per type -->
                                            <div class="shrink-0 mt-0.5">
                                                <template x-if="notif.type === 'thread_reply'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400">
                                                        <i class="fa-regular fa-comment text-xs"></i>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type === 'nested_reply'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-600 dark:text-violet-400">
                                                        <i class="fa-solid fa-reply text-xs"></i>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type === 'article_comment'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-600 dark:text-cyan-400">
                                                        <i class="fa-regular fa-comment-dots text-xs"></i>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type !== 'thread_reply' && notif.type !== 'nested_reply' && notif.type !== 'article_comment'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 border border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400">
                                                        <i class="fa-regular fa-bell text-xs"></i>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow min-w-0">
                                                <template x-if="notif.type === 'thread_reply'">
                                                    <div>
                                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                                            <span class="font-extrabold" x-text="notif.data.replier_name || 'Seseorang'"></span>
                                                            membalas topik Anda:
                                                            <span x-text="notif.data.reply_snippet" class="font-normal text-slate-500 dark:text-slate-400"></span>
                                                        </p>
                                                        <p class="text-[9px] text-slate-450 dark:text-slate-500 mt-0.5 truncate" x-text="'Topik: ' + notif.data.thread_title"></p>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type === 'nested_reply'">
                                                    <div>
                                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors line-clamp-2">
                                                            <span class="font-extrabold" x-text="notif.data.replier_name || 'Seseorang'"></span>
                                                            membalas komentar Anda:
                                                            <span x-text="notif.data.reply_snippet" class="font-normal text-slate-500 dark:text-slate-400"></span>
                                                        </p>
                                                        <p class="text-[9px] text-slate-450 dark:text-slate-500 mt-0.5 truncate" x-text="'Topik: ' + notif.data.thread_title"></p>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type !== 'thread_reply' && notif.type !== 'nested_reply'">
                                                    <div>
                                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                                            <span x-text="notif.data.message || 'Notifikasi baru'"></span>
                                                        </p>
                                                    </div>
                                                </template>
                                                <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1" x-text="notif.created_at"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @endauth

                        <!-- Shopping Cart Sticky Icon -->
                        <a href="{{ route('cart.index') }}" class="relative p-2.5 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 border border-transparent hover:border-slate-200/50 dark:hover:border-slate-800 transition-all">
                            <i class="fa-solid fa-cart-shopping text-base"></i>
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-950 animate-pulse">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Auth Scaffolding / User Dropdown -->
                        @auth
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 border border-transparent hover:border-slate-200/50 dark:hover:border-slate-800 transition-all">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="h-7 w-7 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-xs font-semibold text-white">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="hidden sm:inline text-sm font-medium text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                                </button>
                                
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-800 dark:bg-slate-900 z-50">
                                    
                                    @hasanyrole('superadmin|author|admin-marketplace')
                                        <a href="{{ route('cms.dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                                            <i class="fa-solid fa-gauge-high w-4"></i> Dashboard CMS
                                        </a>
                                    @endhasanyrole

                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                                        <i class="fa-regular fa-user w-4"></i> Profil Pengguna
                                    </a>
                                    <a href="{{ route('orders.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-colors">
                                        <i class="fa-solid fa-receipt w-4"></i> Transaksi Saya
                                    </a>
                                    
                                    <div class="h-px bg-slate-200 dark:bg-slate-800 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors text-left">
                                            <i class="fa-solid fa-right-from-bracket w-4"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all">
                                Daftar
                            </a>
                        @endauth

                        <!-- Mobile Menu Button -->
                        <button class="flex items-center justify-center p-2 text-slate-500 md:hidden rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 border border-transparent" x-data="{ openMobile: false }" @click="openMobile = !openMobile; $dispatch('toggle-mobile-menu', { open: openMobile })">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>

                    </div>
                </div>
            </header>

            <!-- Responsive Mobile Drawer Navigation Menu -->
            <div x-data="{ open: false }" @toggle-mobile-menu.window="open = $event.detail.open" x-show="open" x-cloak class="md:hidden fixed inset-0 z-40 flex" role="dialog" aria-modal="true">
                <!-- Background backdrop -->
                <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false; $dispatch('toggle-mobile-menu', { open: false })"></div>

                <!-- Slide-over panel -->
                <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative max-w-xs w-full bg-white dark:bg-slate-900 shadow-xl flex flex-col p-6 overflow-y-auto">
                    <div class="flex items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
                        <a href="/" class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-semibold shadow-md">
                                <i class="fa-solid fa-microchip text-sm"></i>
                            </div>
                            <span class="text-lg font-bold text-slate-900 dark:text-white">DevGate</span>
                        </a>
                        <button @click="open = false; $dispatch('toggle-mobile-menu', { open: false })" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <nav class="mt-6 flex flex-col gap-4 text-base font-semibold">
                        <a href="{{ route('blog.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-all">
                            <i class="fa-regular fa-newspaper text-indigo-500 w-5"></i> Blog & Artikel
                        </a>
                        <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-all">
                            <i class="fa-solid fa-store text-indigo-500 w-5"></i> Toko IoT & Elektronik
                        </a>
                        <a href="{{ route('forum.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition-all">
                            <i class="fa-solid fa-comments text-indigo-500 w-5"></i> Forum Diskusi
                        </a>
                        
                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-2"></div>
                        
                        @guest
                            <a href="{{ route('login') }}" class="flex items-center justify-center rounded-xl border border-slate-300 dark:border-slate-700 py-2.5 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="flex items-center justify-center rounded-xl bg-indigo-600 py-2.5 text-white font-semibold shadow-md hover:bg-indigo-500 transition-colors">
                                Daftar
                            </a>
                        @endguest
                    </nav>
                </div>
            </div>

            <!-- Page Main Content Slot -->
            <main class="flex-grow">
                @yield('content')
            </main>

            <!-- Sticky Toast Flash Alerts -->
            <div x-data="{ 
                messages: [],
                addMessage(text, type) {
                    const id = Date.now();
                    this.messages.push({ id, text, type });
                    setTimeout(() => this.removeMessage(id), 5000);
                },
                removeMessage(id) {
                    this.messages = this.messages.filter(m => m.id !== id);
                }
            }" 
            x-init="
                @if(session('success'))
                    addMessage('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                    addMessage('{{ session('error') }}', 'error');
                @endif
                @if($errors->any())
                    addMessage('{{ $errors->first() }}', 'error');
                @endif
                window.addEventListener('flash-message', e => {
                    addMessage(e.detail.text, e.detail.type || 'success');
                });
            "
            class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full">
                <template x-for="message in messages" :key="message.id">
                    <div x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         :class="{
                            'bg-emerald-50 text-emerald-900 border-emerald-200 dark:bg-emerald-950/90 dark:text-emerald-300 dark:border-emerald-800/80': message.type === 'success',
                            'bg-rose-50 text-rose-900 border-rose-200 dark:bg-rose-950/90 dark:text-rose-300 dark:border-rose-800/80': message.type === 'error'
                         }"
                         class="flex items-start gap-3 rounded-xl border p-4 shadow-lg backdrop-blur-md">
                        <i :class="message.type === 'success' ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-circle-exclamation text-rose-500'" class="mt-0.5 text-lg"></i>
                        <div class="flex-grow">
                            <p class="text-sm font-medium" x-text="message.text"></p>
                        </div>
                        <button @click="removeMessage(message.id)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Footer Section -->
            <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/40 py-12 transition-colors duration-300">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        
                        <!-- Col 1: Brand Info -->
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-semibold">
                                    <i class="fa-solid fa-microchip text-sm"></i>
                                </div>
                                <span class="text-lg font-bold text-slate-900 dark:text-white">DevGate</span>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                                Platform terintegrasi IoT Specialist, Embedded System, Automation, dan Web Development.
                            </p>
                            <div class="flex items-center gap-3 text-slate-400 dark:text-slate-500 text-lg">
                                <a href="#" class="hover:text-indigo-500 transition-colors"><i class="fa-brands fa-github"></i></a>
                                <a href="#" class="hover:text-indigo-500 transition-colors"><i class="fa-brands fa-linkedin"></i></a>
                                <a href="#" class="hover:text-indigo-500 transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
                                <a href="#" class="hover:text-indigo-500 transition-colors"><i class="fa-brands fa-youtube"></i></a>
                            </div>
                        </div>

                        <!-- Col 2: Navigation Links -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Navigasi</h3>
                            <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <li><a href="{{ route('blog.index') }}" class="hover:text-indigo-500 transition-colors">Blog Teknologi</a></li>
                                <li><a href="{{ route('shop.index') }}" class="hover:text-indigo-500 transition-colors">Toko Komponen IoT</a></li>
                                <li><a href="{{ route('cart.index') }}" class="hover:text-indigo-500 transition-colors">Keranjang Belanja</a></li>
                            </ul>
                        </div>

                        <!-- Col 3: Topics / Categories -->
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4">Topik Utama</h3>
                            <ul class="space-y-2 text-sm text-slate-500 dark:text-slate-400">
                                <li><span class="hover:text-indigo-500 cursor-pointer transition-colors"><i class="fa-solid fa-angle-right mr-1 text-[10px]"></i> IoT Specialist</span></li>
                                <li><span class="hover:text-indigo-500 cursor-pointer transition-colors"><i class="fa-solid fa-angle-right mr-1 text-[10px]"></i> Web Development</span></li>
                                <li><span class="hover:text-indigo-500 cursor-pointer transition-colors"><i class="fa-solid fa-angle-right mr-1 text-[10px]"></i> Embedded System</span></li>
                            </ul>
                        </div>

                        <!-- Col 4: Newsletter Box -->
                        <div class="flex flex-col gap-4">
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Berlangganan Newsletter</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Dapatkan artikel terbaru dan info rilis produk komponen IoT langsung di kotak masuk Anda.</p>
                            
                            <!-- Newsletter Submission Local Form -->
                            <form x-data="newsletterForm()" @submit.prevent="submitForm" class="flex gap-2 relative">
                                <input type="email" x-model="email" placeholder="Alamat email Anda" required class="flex-grow rounded-xl border border-slate-200 bg-white/50 px-3.5 py-2 text-sm dark:border-slate-800 dark:bg-slate-900/50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" :disabled="submitting">
                                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 transition-colors shadow-md shadow-indigo-600/10" :disabled="submitting">
                                    <i x-show="!submitting" class="fa-regular fa-paper-plane"></i>
                                    <i x-show="submitting" class="fa-solid fa-spinner animate-spin" x-cloak></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-12 border-t border-slate-100 dark:border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 dark:text-slate-500">
                        <p>&copy; {{ date('Y') }} DevGate. Hak Cipta Dilindungi.</p>
                        <div class="flex gap-4 mt-2 sm:mt-0">
                            <a href="#" class="hover:underline">Kebijakan Privasi</a>
                            <a href="#" class="hover:underline">Syarat & Ketentuan</a>
                        </div>
                    </div>
                </div>
            </footer>

        </div>

        @yield('scripts')

        @auth
        <script>
        function publicNotificationBell() {
            return {
                open: false,
                count: 0,
                notifications: [],
                loading: false,
                pollInterval: null,

                init() {
                    this.fetchCount();
                    // Poll every 30 seconds
                    this.pollInterval = setInterval(() => this.fetchCount(), 30000);
                },

                async fetchCount() {
                    try {
                        const res = await fetch('{{ route('notifications.index') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        this.count = data.count;
                        if (this.open) {
                            this.notifications = data.notifications;
                        }
                    } catch (e) { /* silent */ }
                },

                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('notifications.index') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        this.count = data.count;
                        this.notifications = data.notifications;
                    } catch (e) { /* silent */ } finally {
                        this.loading = false;
                    }
                },

                async markAllRead() {
                    try {
                        await fetch('{{ route('notifications.readAll') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        this.count = 0;
                        this.notifications = [];
                    } catch (e) { /* silent */ }
                },
            };
        }
        </script>
        @endauth

        <script>
        function newsletterForm() {
            return {
                email: '',
                submitting: false,
                async submitForm() {
                    if (this.submitting) return;
                    this.submitting = true;
                    try {
                        const response = await fetch('{{ route('newsletter.subscribe') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ email: this.email })
                        });
                        const data = await response.json();
                        window.dispatchEvent(new CustomEvent('flash-message', {
                            detail: {
                                text: data.message || 'Terjadi kesalahan.',
                                type: response.ok && data.success !== false ? 'success' : 'error'
                            }
                        }));
                        if (response.ok && data.success !== false) {
                            this.email = '';
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('flash-message', {
                            detail: {
                                text: 'Gagal menghubungi server. Silakan coba beberapa saat lagi.',
                                type: 'error'
                            }
                        }));
                    } finally {
                        this.submitting = false;
                    }
                }
            };
        }
        </script>
        <x-chat-widget />
    </body>
</html>
