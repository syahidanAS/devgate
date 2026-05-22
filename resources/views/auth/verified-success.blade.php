<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Terverifikasi — DevGate</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Outfit', sans-serif; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(-2deg); }
        }
        @keyframes pop-in {
            0% { transform: scale(0.5) rotate(-10deg); opacity: 0; }
            70% { transform: scale(1.1) rotate(3deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2.5); opacity: 0; }
        }
        @keyframes slide-up {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes bounce-in {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            75% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-float-slow { animation: float-slow 6s ease-in-out infinite; }
        .animate-pop-in { animation: pop-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .animate-slide-up { animation: slide-up 0.6s ease forwards; }
        .animate-bounce-in { animation: bounce-in 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }

        .shimmer-text {
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4, #6366f1);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite;
        }

        .confetti-piece {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            animation: confetti-fall linear forwards;
        }

        .pulse-ring {
            animation: pulse-ring 1.5s ease-out infinite;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-950 flex items-center justify-center overflow-hidden relative">

    {{-- Ambient Background Lights --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full bg-indigo-600/20 blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full bg-violet-600/20 blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-cyan-500/5 blur-[150px]"></div>
    </div>

    {{-- Confetti Container --}}
    <div id="confetti-container" class="pointer-events-none"></div>

    {{-- Main Card --}}
    <div class="relative z-10 w-full max-w-lg mx-4">

        {{-- Floating decorative elements --}}
        <div class="absolute -top-12 -left-8 text-4xl animate-float opacity-60" style="animation-delay: 0s">⭐</div>
        <div class="absolute -top-6 -right-10 text-3xl animate-float-slow opacity-60" style="animation-delay: 1s">✨</div>
        <div class="absolute -bottom-8 -left-6 text-2xl animate-float opacity-50" style="animation-delay: 2s">🎉</div>
        <div class="absolute -bottom-4 -right-8 text-3xl animate-float-slow opacity-60" style="animation-delay: 0.5s">🚀</div>

        {{-- Glass Card --}}
        <div class="rounded-3xl border border-slate-700/60 bg-slate-900/70 backdrop-blur-xl shadow-2xl shadow-black/50 overflow-hidden">

            {{-- Top gradient bar --}}
            <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-500"></div>

            <div class="p-8 sm:p-10 text-center">

                {{-- Checkmark Icon with pulsing ring --}}
                <div class="relative inline-flex items-center justify-center mb-8" style="animation-delay: 0.1s">
                    {{-- Pulse rings --}}
                    <div class="absolute inset-0 rounded-full bg-emerald-500/30 pulse-ring" style="animation-delay: 0s"></div>
                    <div class="absolute inset-0 rounded-full bg-emerald-500/20 pulse-ring" style="animation-delay: 0.5s"></div>

                    {{-- Icon container --}}
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 shadow-xl shadow-emerald-500/40 animate-bounce-in">
                        <i class="fa-solid fa-circle-check text-5xl text-white"></i>
                    </div>
                </div>

                {{-- Heading --}}
                <div class="animate-slide-up" style="animation-delay: 0.2s; animation-fill-mode: both;">
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">
                        <i class="fa-solid fa-shield-check mr-1.5"></i> Verifikasi Berhasil
                    </p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-3">
                        Selamat Datang di<br>
                        <span class="shimmer-text">DevGate! 🎊</span>
                    </h1>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-sm mx-auto">
                        Email kamu <strong class="text-white">{{ Auth::user()->email }}</strong> telah berhasil diverifikasi.
                        Akun kamu sudah aktif dan siap digunakan!
                    </p>
                </div>

                {{-- Divider --}}
                <div class="my-8 border-t border-slate-800/60"></div>

                {{-- User Info Card --}}
                <div class="animate-slide-up mb-8 rounded-2xl bg-slate-800/50 border border-slate-700/40 p-4 flex items-center gap-4 text-left" style="animation-delay: 0.35s; animation-fill-mode: both;">
                    <div class="relative shrink-0">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366f1&color=fff&size=80' }}"
                             alt="{{ Auth::user()->name }}"
                             class="h-14 w-14 rounded-2xl object-cover ring-2 ring-indigo-500/40">
                        <div class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 border-2 border-slate-800">
                            <i class="fa-solid fa-check text-[8px] text-white"></i>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        <span class="inline-flex items-center gap-1 mt-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 px-2 py-px text-[10px] font-bold text-emerald-400">
                            <span class="h-1 w-1 rounded-full bg-emerald-500"></span> Terverifikasi
                        </span>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="animate-slide-up space-y-3" style="animation-delay: 0.5s; animation-fill-mode: both;">
                    <a href="{{ route('blog.index') }}"
                       class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-[1.02] transition-all duration-200">
                        <i class="fa-solid fa-house text-base"></i>
                        Jelajahi DevGate
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-800/60 px-6 py-3.5 text-sm font-semibold text-slate-300 hover:text-white hover:border-slate-600 hover:bg-slate-800 transition-all duration-200">
                        <i class="fa-solid fa-store text-base text-cyan-400"></i>
                        Kunjungi Marketplace IoT
                    </a>
                </div>

                {{-- Fun fact / tip --}}
                <div class="animate-slide-up mt-6" style="animation-delay: 0.65s; animation-fill-mode: both;">
                    <p class="text-[11px] text-slate-600 flex items-center justify-center gap-1.5">
                        <i class="fa-regular fa-lightbulb text-amber-500/70"></i>
                        Tip: Lengkapi profil kamu agar lebih mudah dikenal komunitas DevGate
                    </p>
                </div>
            </div>

            {{-- Bottom DevGate branding --}}
            <div class="border-t border-slate-800/60 bg-slate-950/30 px-8 py-4 flex items-center justify-center gap-2">
                <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-gradient-to-tr from-indigo-500 to-cyan-500 text-white">
                    <i class="fa-solid fa-microchip text-[10px]"></i>
                </div>
                <span class="text-xs font-bold text-slate-500">Dev<span class="text-indigo-400">Gate</span> Platform</span>
            </div>
        </div>
    </div>

    <script>
        // Launch confetti on page load
        function launchConfetti() {
            const colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#f43f5e','#ffffff'];
            const container = document.getElementById('confetti-container');
            const count = 80;

            for (let i = 0; i < count; i++) {
                const piece = document.createElement('div');
                piece.classList.add('confetti-piece');

                const size = Math.random() * 10 + 5;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const left = Math.random() * 100;
                const delay = Math.random() * 3;
                const duration = Math.random() * 3 + 2;
                const isCircle = Math.random() > 0.5;

                piece.style.cssText = `
                    left: ${left}vw;
                    top: -20px;
                    width: ${size}px;
                    height: ${size}px;
                    background: ${color};
                    border-radius: ${isCircle ? '50%' : '2px'};
                    animation-duration: ${duration}s;
                    animation-delay: ${delay}s;
                `;
                container.appendChild(piece);

                // Remove after animation
                setTimeout(() => piece.remove(), (duration + delay) * 1000 + 500);
            }
        }

        // Run confetti after a short delay for dramatic effect
        setTimeout(launchConfetti, 400);
        setTimeout(launchConfetti, 1800);
    </script>
</body>
</html>
