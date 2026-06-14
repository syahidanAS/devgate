<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true, mobileSidebarOpen: false }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'CMS Portal — DevGate')</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- FontAwesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Highlight.js for Syntax Highlighting -->
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
    <body class="antialiased min-h-screen bg-slate-900 text-slate-100 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">

        
        <!-- Ambient Cyberpunk Lights -->
        <div class="relative overflow-hidden min-h-screen flex">
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] pointer-events-none dark:bg-indigo-900/15"></div>
            <div class="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-cyan-500/10 blur-[150px] pointer-events-none dark:bg-cyan-900/15"></div>

            <!-- 1. SIDEBAR (Collapsible Desktop / Mobile Drawer) -->
            <aside 
                class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-slate-950/80 backdrop-blur-xl border-r border-slate-800/80 transition-all duration-300 transform"
                :class="{ 
                    'w-64': sidebarOpen, 
                    'w-20': !sidebarOpen, 
                    'translate-x-0': mobileSidebarOpen, 
                    '-translate-x-full md:translate-x-0': !mobileSidebarOpen 
                }"
            >
                <!-- Brand logo area -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800/80 overflow-hidden">
                    <a href="/" class="flex items-center gap-2 group whitespace-nowrap">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-500 to-cyan-500 text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-all">
                            <i class="fa-solid fa-microchip text-lg"></i>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">
                            Dev<span class="text-indigo-400 font-extrabold">Gate</span><span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">CMS</span>
                        </span>
                    </a>
                    
                    <!-- Mobile sidebar close btn -->
                    <button @click="mobileSidebarOpen = false" class="md:hidden p-1 text-slate-400 hover:text-white rounded-lg hover:bg-slate-850">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Role Indication Overlay Card -->
                <div class="p-4 border-b border-slate-800/60 overflow-hidden shrink-0" :class="{ 'px-4': sidebarOpen, 'px-2 text-center': !sidebarOpen }">
                    <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-3 flex items-center gap-3">
                        <div class="h-9 w-9 shrink-0 flex items-center justify-center rounded-lg bg-indigo-600/30 border border-indigo-500/40 text-xs font-bold text-indigo-400 uppercase">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left overflow-hidden transition-all duration-300" :class="{ 'opacity-100 max-w-full': sidebarOpen, 'opacity-0 max-w-0 hidden': !sidebarOpen }">
                            <h4 class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                            <span class="inline-flex items-center text-[10px] font-semibold text-cyan-400 uppercase tracking-wider mt-0.5">
                                @if(Auth::user()->hasRole('superadmin'))
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500 mr-1.5 animate-pulse"></span> Super Admin
                                @elseif(Auth::user()->hasRole('author'))
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> Writer / Author
                                @elseif(Auth::user()->hasRole('admin-marketplace'))
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span> Store Admin
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Sidebar Menus -->
                <nav class="flex-grow py-6 px-3 overflow-y-auto space-y-1 scrollbar-thin scrollbar-thumb-slate-850">
                    
                    <!-- Dashboard Link (Universal) -->
                    <a href="{{ route('cms.dashboard') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.dashboard') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                        <i class="fa-solid fa-chart-pie text-base shrink-0"></i>
                        <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Dashboard</span>
                    </a>

                    <!-- SUPERADMIN MENU SECTION -->
                    @role('superadmin')
                        <div class="pt-4 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition-all duration-300 px-3" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 h-0 py-0 overflow-hidden': !sidebarOpen }">
                            Sistem & User
                        </div>
                        <a href="{{ route('cms.users.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.users.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-users-gear text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Pengguna</span>
                        </a>
                    @endrole

                    <!-- ARTICLE AUTHOR & SUPERADMIN SECTION -->
                    @hasanyrole('superadmin|author')
                        <div class="pt-4 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition-all duration-300 px-3" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 h-0 py-0 overflow-hidden': !sidebarOpen }">
                            Blog & Artikel
                        </div>
                        <a href="{{ route('cms.articles.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.articles.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-pen-nib text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Artikel</span>
                        </a>
                        <a href="{{ route('cms.videos.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.videos.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-clapperboard text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Video</span>
                        </a>
                        <div class="pt-4 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition-all duration-300 px-3" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 h-0 py-0 overflow-hidden': !sidebarOpen }">
                            Flasher Firmware
                        </div>
                        <a href="{{ route('cms.firmware-projects.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.firmware-projects.*') || request()->routeIs('cms.firmware-files.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-bolt text-base shrink-0 text-amber-500"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Firmware</span>
                        </a>
                    @endhasanyrole


                    <!-- MARKETPLACE ADMIN & SUPERADMIN SECTION -->
                    @hasanyrole('superadmin|admin-marketplace')
                        <div class="pt-4 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition-all duration-300 px-3" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 h-0 py-0 overflow-hidden': !sidebarOpen }">
                            Marketplace IoT
                        </div>
                        <a href="{{ route('cms.products.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.products.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-boxes-stacked text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Komponen</span>
                        </a>
                        <a href="{{ route('cms.orders.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.orders.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-receipt text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Kelola Pesanan</span>
                        </a>
                    @endhasanyrole

                    <!-- LIVE CHAT SECTION -->
                    @hasanyrole('superadmin|admin-marketplace|author')
                        <div class="pt-4 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500 transition-all duration-300 px-3" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 h-0 py-0 overflow-hidden': !sidebarOpen }">
                            Dukungan & Chat
                        </div>
                        <a href="{{ route('cms.chats.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all border border-transparent {{ request()->routeIs('cms.chats.*') ? 'bg-indigo-600/20 border-indigo-500/30 text-indigo-400' : 'text-slate-400 hover:text-white hover:bg-slate-900/60' }}">
                            <i class="fa-solid fa-comments text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Live Chat</span>
                        </a>
                    @endhasanyrole

                    <div class="pt-6 border-t border-slate-800/40"></div>

                    <!-- Public Links -->
                    <a href="{{ route('blog.index') }}" target="_blank" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-900/60 border border-transparent transition-all">
                        <i class="fa-solid fa-globe text-base shrink-0 text-cyan-450"></i>
                        <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Lihat Web Publik</span>
                    </a>
                </nav>

                <!-- Logout section at bottom -->
                <div class="p-3 border-t border-slate-800/80 shrink-0">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-400 hover:text-white hover:bg-red-900/20 border border-transparent transition-all">
                            <i class="fa-solid fa-right-from-bracket text-base shrink-0"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- 2. MAIN CONTENT WRAPPER -->
            <div 
                class="flex-grow min-h-screen flex flex-col transition-all duration-300 pl-0 md:pl-20"
                :class="{ 'md:pl-64': sidebarOpen, 'md:pl-20': !sidebarOpen }"
            >
                
                <!-- TOP HEADER -->
                <header class="h-16 shrink-0 bg-slate-950/40 border-b border-slate-800/60 backdrop-blur-md sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-4">
                        <!-- Desktop Sidebar toggle button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden md:inline-flex p-2 text-slate-400 hover:text-white hover:bg-slate-900/60 border border-slate-800/60 rounded-xl transition-all">
                            <i class="fa-solid fa-bars-staggered text-base transition-transform" :class="{ 'rotate-180': !sidebarOpen }"></i>
                        </button>
                        
                        <!-- Mobile Sidebar toggle button -->
                        <button @click="mobileSidebarOpen = true" class="md:hidden p-2 text-slate-400 hover:text-white hover:bg-slate-900/60 border border-slate-800/60 rounded-xl transition-all">
                            <i class="fa-solid fa-bars text-base"></i>
                        </button>

                        <div class="hidden sm:block">
                            @yield('breadcrumbs')
                        </div>
                    </div>

                    <!-- Right Header Utilities -->
                    <div class="flex items-center gap-4">
                        
                        <!-- Dark Mode Toggle (Global State Sync) -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2.5 text-slate-400 hover:text-white rounded-xl hover:bg-slate-900/60 border border-slate-800/60 transition-all" aria-label="Toggle Theme">
                            <i class="fa-solid fa-sun text-base scale-0 transition-transform dark:scale-100 absolute" :class="{ 'scale-100': !darkMode, 'scale-0': darkMode }"></i>
                            <i class="fa-solid fa-moon text-base dark:scale-0 transition-transform scale-100" :class="{ 'scale-100': darkMode, 'scale-0': !darkMode }"></i>
                        </button>

                        <!-- ── Notification Bell ── -->
                        <div class="relative"
                             x-data="notificationBell()"
                             x-init="init()"
                             @click.outside="open = false">

                            <!-- Bell Button -->
                            <button @click="open = !open; if(open) fetchNotifications()"
                                    class="relative p-2.5 text-slate-400 hover:text-white rounded-xl hover:bg-slate-900/60 border border-slate-800/60 transition-all"
                                    aria-label="Notifikasi">
                                <i class="fa-solid fa-bell text-base"></i>
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
                                 class="absolute right-0 top-full mt-3 w-80 rounded-2xl border border-slate-700/80 bg-slate-900/95 backdrop-blur-xl shadow-2xl shadow-black/50 z-50 overflow-hidden">

                                <!-- Header -->
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-bell text-xs text-indigo-400"></i>
                                        <span class="text-xs font-bold text-white">Notifikasi</span>
                                        <span x-show="count > 0"
                                              x-text="count + ' baru'"
                                              class="inline-flex items-center rounded-full bg-rose-500/20 border border-rose-500/30 px-1.5 py-0.5 text-[9px] font-bold text-rose-400"
                                              x-cloak></span>
                                    </div>
                                    <button x-show="count > 0"
                                            @click="markAllRead()"
                                            class="text-[10px] font-semibold text-slate-500 hover:text-indigo-400 transition-colors"
                                            x-cloak>
                                        Tandai Semua Dibaca
                                    </button>
                                </div>

                                <!-- Notification List -->
                                <div class="max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-800">

                                    <!-- Loading state -->
                                    <div x-show="loading" class="flex items-center justify-center py-8 gap-2 text-slate-500 text-xs">
                                        <i class="fa-solid fa-spinner animate-spin text-indigo-400"></i>
                                        <span>Memuat notifikasi...</span>
                                    </div>

                                    <!-- Empty state -->
                                    <div x-show="!loading && notifications.length === 0" class="flex flex-col items-center py-8 text-slate-600">
                                        <i class="fa-regular fa-bell-slash text-3xl mb-2"></i>
                                        <p class="text-xs font-semibold text-slate-500">Tidak ada notifikasi baru</p>
                                    </div>

                                    <!-- Notifications -->
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <a :href="'{{ url('cms/notifications') }}/' + notif.id + '/read'"
                                           class="flex items-start gap-3 px-4 py-3 border-b border-slate-800/40 hover:bg-slate-800/40 transition-colors group cursor-pointer">

                                            <!-- Icon per type -->
                                            <div class="shrink-0 mt-0.5">
                                                <template x-if="notif.type === 'article_comment'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-500/15 border border-indigo-500/20 text-indigo-400">
                                                        <i class="fa-regular fa-comment text-xs"></i>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type === 'new_order'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-400">
                                                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                                                    </div>
                                                </template>
                                                <template x-if="notif.type !== 'article_comment' && notif.type !== 'new_order'">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-700/50 border border-slate-700 text-slate-400">
                                                        <i class="fa-solid fa-bell text-xs"></i>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-grow min-w-0">
                                                <!-- Comment notification -->
                                                <template x-if="notif.type === 'article_comment'">
                                                    <div>
                                                        <p class="text-[11px] font-semibold text-white leading-snug truncate">
                                                            <span x-text="notif.data.commenter_name" class="text-indigo-300"></span>
                                                            mengomentari
                                                            <span x-text="'«' + notif.data.article_title + '»'" class="text-slate-300"></span>
                                                        </p>
                                                        <p class="text-[10px] text-slate-500 mt-0.5 line-clamp-1" x-text="notif.data.comment_snippet"></p>
                                                        <span x-show="notif.data.comment_status === 'pending'"
                                                              class="inline-flex items-center gap-1 mt-1 rounded-full bg-amber-500/15 border border-amber-500/25 px-1.5 py-px text-[8px] font-bold text-amber-400">
                                                            <i class="fa-solid fa-clock text-[7px]"></i> Menunggu moderasi
                                                        </span>
                                                    </div>
                                                </template>

                                                <!-- Order notification -->
                                                <template x-if="notif.type === 'new_order'">
                                                    <div>
                                                        <p class="text-[11px] font-semibold text-white leading-snug">
                                                            Pesanan baru dari <span class="text-emerald-300" x-text="notif.data.buyer_name"></span>
                                                        </p>
                                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                                            <span x-text="notif.data.order_number" class="font-mono"></span>
                                                            · <span x-text="notif.data.total_fmt" class="text-emerald-400 font-semibold"></span>
                                                        </p>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Time -->
                                            <span class="shrink-0 text-[9px] text-slate-600 group-hover:text-slate-400 transition-colors mt-0.5" x-text="notif.created_at"></span>
                                        </a>
                                    </template>
                                </div>

                                <!-- Footer -->
                                <div class="px-4 py-2.5 border-t border-slate-800/60 bg-slate-950/40">
                                    <p class="text-[9px] text-slate-600 text-center">
                                        <i class="fa-solid fa-rotate text-[8px] mr-1"></i>
                                        Diperbarui otomatis setiap 30 detik
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </header>

                <!-- PAGE MAIN CONTAINER -->
                <main class="flex-grow p-4 sm:p-6 lg:p-8">
                    
                    <!-- Flash Message Components -->
                    @if(session('success'))
                        <div class="mb-6 flex items-center justify-between rounded-2xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-sm text-emerald-450 dark:bg-emerald-950/20 dark:text-emerald-400">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 flex items-center justify-between rounded-2xl bg-red-500/10 border border-red-500/30 p-4 text-sm text-red-450 dark:bg-red-950/20 dark:text-red-400">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </main>

                <!-- Portal Dashboard Footer -->
                <footer class="h-14 border-t border-slate-850 shrink-0 bg-slate-950/20 flex items-center justify-between px-6 text-xs text-slate-500">
                    <p>&copy; {{ date('Y') }} DevGate Platform. All rights reserved. <span class="ml-2 font-semibold text-indigo-400">Beta Version</span></p>
                    <p class="hidden sm:block">IoT Specialist, AI, WebDev Dashboard</p>
                </footer>

            </div>
        </div>

        <!-- Mobile sidebar drawer back-overlay -->
        <div 
            class="fixed inset-0 z-30 bg-slate-950/65 backdrop-blur-sm transition-opacity duration-300 md:hidden"
            x-show="mobileSidebarOpen"
            @click="mobileSidebarOpen = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        ></div>

        @yield('scripts')

        <script>
        function notificationBell() {
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
                        const res = await fetch('{{ route('cms.notifications.index') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        this.count = data.count;
                        if (this.open) {
                            this.notifications = data.notifications;
                        }
                    } catch (e) { /* network error — silent */ }
                },

                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('cms.notifications.index') }}', {
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
                        await fetch('{{ route('cms.notifications.readAll') }}', {
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
    </body>
</html>
