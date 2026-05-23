<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'DevGate') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { font-family: 'Outfit', sans-serif; min-height: 100vh; background: #030712; color: #f1f5f9; }

        /* ── Background ── */
        .bg-scene { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .bg-grid {
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(99,102,241,.06) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(99,102,241,.06) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: pulse-orb 6s ease-in-out infinite; }
        .orb-1 { width: 520px; height: 520px; top: -10%; left: 10%; background: radial-gradient(circle, rgba(99,102,241,.3) 0%, transparent 70%); }
        .orb-2 { width: 600px; height: 600px; bottom: -15%; right: 5%; background: radial-gradient(circle, rgba(139,92,246,.25) 0%, transparent 70%); animation-delay: 2s; }
        .orb-3 { width: 400px; height: 400px; top: 40%; left: -5%; background: radial-gradient(circle, rgba(6,182,212,.15) 0%, transparent 70%); animation-delay: 4s; }
        .ring { position: absolute; border-radius: 50%; border: 1px solid rgba(99,102,241,.08); animation: spin-ring 25s linear infinite; }
        .ring-1 { width: 320px; height: 320px; top: 4%; right: 4%; }
        .ring-2 { width: 200px; height: 200px; bottom: 8%; left: 4%; animation-direction: reverse; animation-duration: 35s; }

        @keyframes pulse-orb { 0%,100%{opacity:.5;transform:scale(1)} 50%{opacity:.8;transform:scale(1.06)} }
        @keyframes spin-ring { from{transform:rotate(0)} to{transform:rotate(360deg)} }
        @keyframes float-dot { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
        @keyframes card-in { from{opacity:0;transform:translateY(20px) scale(.98)} to{opacity:1;transform:translateY(0) scale(1)} }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }

        /* ── Two-pane layout ── */
        .page-wrap { position: relative; z-index: 1; min-height: 100vh; display: flex; }

        /* Left hero */
        .hero-pane {
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 4rem 3rem 4rem 4rem;
            width: 45%;
        }
        @media (min-width: 1024px) { .hero-pane { display: flex; } }

        .hero-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 3rem; text-decoration: none; }
        .hero-logo-icon {
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg, #6366f1, #06b6d4);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
            box-shadow: 0 4px 20px rgba(99,102,241,.4);
        }
        .hero-logo-text { font-size: 22px; font-weight: 800; color: white; letter-spacing: -.3px; }
        .hero-logo-text span { color: #818cf8; }

        .hero-headline { font-size: clamp(2rem, 3vw, 2.8rem); font-weight: 900; line-height: 1.1; letter-spacing: -.5px; color: white; margin-bottom: 1rem; }
        .shimmer-text {
            background: linear-gradient(90deg, #818cf8, #a78bfa, #38bdf8, #818cf8);
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: shimmer 3.5s linear infinite;
        }
        .hero-sub { color: #64748b; font-size: .95rem; line-height: 1.6; margin-bottom: 2.5rem; max-width: 360px; }

        .feature-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 2.5rem; }
        .feature-item {
            display: flex; align-items: center; gap: 14px;
            background: rgba(15,23,42,.6); border: 1px solid rgba(51,65,85,.5);
            border-radius: 16px; padding: 14px 16px;
            transition: border-color .2s;
        }
        .feature-item:hover { border-color: rgba(99,102,241,.3); }
        .feature-icon {
            width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        .fi-indigo { background: rgba(99,102,241,.15); border: 1px solid rgba(99,102,241,.2); color: #818cf8; }
        .fi-violet { background: rgba(139,92,246,.15); border: 1px solid rgba(139,92,246,.2); color: #a78bfa; }
        .fi-cyan   { background: rgba(6,182,212,.15);  border: 1px solid rgba(6,182,212,.2);  color: #22d3ee; }
        .feature-title { font-size: .875rem; font-weight: 700; color: white; margin-bottom: 2px; }
        .feature-desc  { font-size: .75rem; color: #475569; }

        .trust-row { display: flex; align-items: center; gap: 10px; }
        .avatars { display: flex; }
        .av { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #030712; font-size: 10px; font-weight: 700; color: white; display: flex; align-items: center; justify-content: center; margin-left: -8px; }
        .av:first-child { margin-left: 0; }
        .trust-text { font-size: .75rem; color: #475569; }
        .trust-text b { color: white; }

        /* ── Right form pane ── */
        .form-pane {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2.5rem 1.25rem;
        }
        .mobile-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 2rem; text-decoration: none; }
        @media (min-width: 1024px) { .mobile-logo { display: none; } }
        .mobile-logo-icon { width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg,#6366f1,#06b6d4); display:flex;align-items:center;justify-content:center;font-size:15px;color:white; }
        .mobile-logo-text { font-size: 18px; font-weight: 800; color: white; }
        .mobile-logo-text span { color: #818cf8; }

        .auth-card {
            width: 100%; max-width: 420px;
            border-radius: 24px; border: 1px solid rgba(51,65,85,.6);
            background: rgba(15,23,42,.85); backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0,0,0,.6);
            overflow: hidden;
            animation: card-in .5s cubic-bezier(.16,1,.3,1) both;
        }
        .card-top-bar { height: 4px; background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4); }
        .card-body { padding: 2rem 2rem 1.75rem; }
        .card-footer {
            border-top: 1px solid rgba(30,41,59,.8);
            background: rgba(3,7,18,.3);
            padding: .875rem 2rem; text-align: center;
            font-size: 11px; color: #334155;
        }
        .card-footer a { color: #475569; text-decoration: none; transition: color .2s; }
        .card-footer a:hover { color: #818cf8; }

        /* ── Form elements ── */
        .auth-heading { font-size: 1.6rem; font-weight: 800; color: white; margin-bottom: 4px; }
        .auth-sub { font-size: .875rem; color: #475569; margin-bottom: 1.5rem; }
        .auth-sub a { color: #818cf8; font-weight: 600; text-decoration: none; }
        .auth-sub a:hover { color: #a5b4fc; }

        .google-btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: .75rem 1rem; border-radius: 14px;
            border: 1px solid rgba(51,65,85,.8); background: rgba(30,41,59,.7);
            color: #cbd5e1; font-size: .875rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: background .2s, border-color .2s, color .2s, box-shadow .2s;
            margin-bottom: 1.25rem;
        }
        .google-btn:hover { background: rgba(51,65,85,.7); border-color: rgba(99,102,241,.3); color: white; box-shadow: 0 4px 16px rgba(0,0,0,.3); }
        .google-btn svg { width: 20px; height: 20px; flex-shrink: 0; }

        .divider { position: relative; margin-bottom: 1.25rem; }
        .divider::before { content:''; position:absolute; inset: 50% 0 50%; border-top: 1px solid rgba(30,41,59,.9); }
        .divider span {
            position: relative; background: rgba(15,23,42,.95);
            padding: 0 .75rem; font-size: 10px; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase; color: #334155;
        }
        .divider-inner { display: flex; justify-content: center; }

        .form-grid { display: flex; flex-direction: column; gap: 1rem; }
        .field-label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #475569; margin-bottom: 6px; }
        .field-row-label { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
        .forgot-link { font-size: 11px; font-weight: 600; color: #818cf8; text-decoration: none; }
        .forgot-link:hover { color: #a5b4fc; }

        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #334155; font-size: 13px; pointer-events: none; }
        .auth-input {
            width: 100%; background: rgba(15,23,42,.8);
            border: 1px solid rgba(51,65,85,.7); border-radius: 12px;
            padding: .65rem 1rem .65rem 2.6rem;
            color: #f1f5f9; font-size: .875rem; font-family: 'Outfit', sans-serif;
            transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .auth-input::placeholder { color: #1e293b; }
        .auth-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
        .auth-input.no-icon { padding-left: 1rem; }
        .toggle-pw { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #334155; cursor: pointer; font-size: 13px; transition: color .2s; }
        .toggle-pw:hover { color: #64748b; }

        .remember-row { display: flex; align-items: center; gap: 8px; }
        .remember-row input[type=checkbox] { width: 15px; height: 15px; accent-color: #6366f1; border-radius: 4px; cursor: pointer; flex-shrink: 0; }
        .remember-row label { font-size: .8125rem; color: #475569; cursor: pointer; }

        .captcha-wrap { display: flex; align-items: center; gap: 10px; }
        .captcha-img-box {
            border-radius: 10px; overflow: hidden; border: 1px solid rgba(51,65,85,.7);
            cursor: pointer; flex-shrink: 0; transition: border-color .2s;
        }
        .captcha-img-box:hover { border-color: rgba(99,102,241,.4); }
        .captcha-img-box img { height: 44px; display: block; }
        .captcha-hint { font-size: 10px; color: #334155; margin-top: 5px; }

        .submit-btn {
            width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: .8rem 1.5rem; border-radius: 14px; border: none;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white; font-size: .875rem; font-weight: 700; font-family: 'Outfit', sans-serif;
            cursor: pointer; transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 20px rgba(99,102,241,.45);
            margin-top: 4px;
        }
        .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(99,102,241,.55); }
        .submit-btn:active { transform: translateY(0); }

        .error-msg { font-size: 11px; color: #f87171; margin-top: 5px; display: flex; align-items: center; gap: 4px; }
        .flash-error {
            display: flex; align-items: center; gap: 8px;
            background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
            border-radius: 12px; padding: .75rem 1rem; margin-bottom: 1rem;
            font-size: .8125rem; font-weight: 600; color: #f87171;
        }
        .flash-success {
            display: flex; align-items: center; gap: 8px;
            background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3);
            border-radius: 12px; padding: .75rem 1rem; margin-bottom: 1rem;
            font-size: .8125rem; font-weight: 600; color: #34d399;
        }
        .terms-note { font-size: 11px; color: #334155; line-height: 1.5; }
        .terms-note a { color: #818cf8; text-decoration: none; }
        .terms-note a:hover { text-decoration: underline; }
    </style>
</head>
<body>


{{-- Background Scene --}}
<div class="bg-scene">
    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="ring ring-1"></div>
    <div class="ring ring-2"></div>
</div>

<div class="page-wrap">

    {{-- Left Hero Pane --}}
    <div class="hero-pane">
        <a href="{{ url('/') }}" class="hero-logo">
            <div class="hero-logo-icon"><i class="fa-solid fa-microchip"></i></div>
            <span class="hero-logo-text">Dev<span>Gate</span></span>
        </a>

        <h1 class="hero-headline">
            Platform IoT<br>
            <span class="shimmer-text">Developer<br>Indonesia</span>
        </h1>
        <p class="hero-sub">Bergabung dengan ribuan developer IoT, pelajari tutorial terbaru, dan temukan hardware terbaik di marketplace kami.</p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon fi-indigo"><i class="fa-solid fa-graduation-cap"></i></div>
                <div>
                    <div class="feature-title">Tutorial &amp; Artikel</div>
                    <div class="feature-desc">Konten teknis IoT terkurasi dari para expert</div>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon fi-violet"><i class="fa-solid fa-store"></i></div>
                <div>
                    <div class="feature-title">Marketplace Hardware</div>
                    <div class="feature-desc">Komponen elektronik dan modul IoT berkualitas</div>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon fi-cyan"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="feature-title">Komunitas Aktif</div>
                    <div class="feature-desc">Diskusi, berbagi proyek, dan kolaborasi IoT</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Form Pane --}}
    <div class="form-pane">
        <a href="{{ url('/') }}" class="mobile-logo">
            <div class="mobile-logo-icon"><i class="fa-solid fa-microchip"></i></div>
            <span class="mobile-logo-text">Dev<span>Gate</span></span>
        </a>

        <div class="auth-card">
            <div class="card-top-bar"></div>
            <div class="card-body">
                @yield('content')
            </div>
            <div class="card-footer">
                &copy; {{ date('Y') }} DevGate <span class="text-indigo-400 font-semibold ml-1">(Beta Version)</span> &nbsp;·&nbsp;
                <a href="#">Kebijakan Privasi</a> &nbsp;·&nbsp;
                <a href="#">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>

</div>

<x-chat-widget />
</body>
</html>
