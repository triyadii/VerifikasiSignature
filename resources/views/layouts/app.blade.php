<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aplikasi Verifikasi Tanda Tangan Elektronik (TTE) - Sistem pengecekan keaslian dokumen digital">
    <title>@yield('title', 'Verifikasi TTE') — Jasa Izin Verified</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --surface2:  #22263a;
            --border:    #2e3348;
            --accent:    #22c55e; /* green accent instead of purple */
            --accent2:   #7cfa5c; /* secondary green */
            --green:     #22c55e;
            --red:       #ef4444;
            --yellow:    #f59e0b;
            --text:      #ffffff; /* ensure pure white text */
            --muted:     #bbbbbb; /* lighter muted for contrast */
            --sidebar-w: 240px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo h2 {
            font-size: 1.1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-logo p { font-size: .7rem; color: var(--muted); margin-top: 2px; }

        .sidebar-nav { padding: 16px 12px; flex: 1; overflow-y: auto; }

        .nav-section-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            padding: 12px 8px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
        }

        .nav-link:hover { background: var(--surface2); color: var(--text); }
        .nav-link.active { background: rgba(79,124,255,.15); color: var(--accent); }
        .nav-link .icon { font-size: 1rem; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--surface2);
            border-radius: 10px;
        }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name  { font-size: .8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role  { font-size: .68rem; color: var(--muted); text-transform: capitalize; }

        .logout-btn {
            display: block;
            margin-top: 8px;
            width: 100%;
            padding: 8px;
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.25);
            color: var(--red);
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
        }
        .logout-btn:hover { background: rgba(239,68,68,.22); }

        /* ── MAIN ── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: 56px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 12px;
        }

        .topbar h1 { font-size: 1rem; font-weight: 600; color: var(--text); }
        .topbar .breadcrumb { font-size: .8rem; color: var(--muted); }

        .page-content { padding: 28px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
        }

        .card-title { font-size: .95rem; font-weight: 600; margin-bottom: 16px; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: .72rem; font-weight: 600;
        }
        .badge-green  { background: rgba(34,197,94,.15);  color: var(--green); }
        .badge-red    { background: rgba(239,68,68,.15);  color: var(--red); }
        .badge-yellow { background: rgba(245,158,11,.15); color: var(--yellow); }
        .badge-blue   { background: rgba(79,124,255,.15); color: var(--accent); }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        thead th {
            text-align: left;
            padding: 10px 14px;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody td { padding: 12px 14px; border-bottom: 1px solid rgba(46,51,72,.6); vertical-align: middle; }
        tbody tr:hover { background: rgba(255,255,255,.02); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .8rem; font-weight: 500; margin-bottom: 6px; color: var(--muted); }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: .875rem;
            transition: border-color .2s;
            outline: none;
        }
        .form-control:focus { border-color: var(--accent); }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { font-size: .75rem; color: var(--red); margin-top: 4px; }
        select.form-control option { background: var(--surface2); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 8px;
            font-size: .875rem; font-weight: 500;
            cursor: pointer; border: none; transition: all .2s;
            text-decoration: none;
        }
        .btn-primary  { background: var(--accent); color: #fff; }
        .btn-primary:hover  { background: #3d6aff; }
        .btn-danger   { background: rgba(239,68,68,.15); color: var(--red); border: 1px solid rgba(239,68,68,.3); }
        .btn-danger:hover   { background: rgba(239,68,68,.25); }
        .btn-secondary { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); }
        .btn-sm { padding: 5px 12px; font-size: .78rem; }

        /* ── ALERTS ── */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: .875rem; margin-bottom: 16px; }
        .alert-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); color: var(--green); }
        .alert-danger  { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.3);  color: var(--red); }

        /* ── STATS GRID ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }
        .stat-label { font-size: .75rem; color: var(--muted); margin-bottom: 6px; }
        .stat-value { font-size: 1.8rem; font-weight: 700; }
        .stat-green { color: var(--green); }
        .stat-red   { color: var(--red); }
        .stat-blue  { color: var(--accent); }
        .stat-purple { color: var(--accent2); }

        /* ── PAGINATION ── */
        .pagination { display: flex; list-style: none; gap: 6px; padding: 0; margin-top: 16px; justify-content: center; flex-wrap: wrap; }
        .pagination .page-item .page-link {
            display: block;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: .85rem;
            text-decoration: none;
            color: var(--muted);
            background: var(--surface2);
            border: 1px solid var(--border);
            transition: all .2s;
        }
        .pagination .page-item.active .page-link {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }
        .pagination .page-item a.page-link:hover {
            color: var(--text);
            background: var(--border);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
        }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-logo" style="display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid var(--border);">
        <img src="{{ asset('logo.png') }}" style="width: 32px; height: auto;" alt="Logo Jasa Izin Verified">
        <div>
            <h2 style="font-size: 1.05rem; font-weight: 700; background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0; line-height: 1.2;">Jasa Izin Verified</h2>
            <p style="font-size: .65rem; color: var(--muted); margin-top: 1px; line-height: 1.2;">Sistem Verifikasi TTE</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="{{ route('verifikasi.index') }}"
           class="nav-link {{ request()->routeIs('verifikasi.index') ? 'active' : '' }}">
            <span class="icon">📄</span> Verifikasi TTE
        </a>
        @auth
        <a href="{{ route('sertifikat.index') }}"
           class="nav-link {{ request()->routeIs('sertifikat.index') ? 'active' : '' }}">
            <span class="icon">📜</span> Data Sertifikat
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-label">Administrator</div>
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> Dashboard
        </a>
        <a href="{{ route('admin.logs') }}"
           class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
            <span class="icon">📋</span> Log Verifikasi
        </a>
        <a href="{{ route('admin.users') }}"
           class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="icon">👥</span> Manajemen User
        </a>
        @endif
        @endauth
    </nav>

    <div class="sidebar-footer">
        @auth
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">⏻ Keluar</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; display: flex; justify-content: center; gap: 8px;">
            🔑 Masuk Admin
        </a>
        @endauth
    </div>
</aside>

{{-- MAIN CONTENT --}}
<div class="main-wrap">
    <header class="topbar">
        <div>
            <div class="breadcrumb">Jasa Izin Verified</div>
            <h1>@yield('page-title', 'Dashboard')</h1>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
