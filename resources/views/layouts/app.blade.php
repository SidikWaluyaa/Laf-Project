<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — LAF Inventory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css"></noscript>
    <style>
        /* ===== OBSIDIAN EXECUTIVE DESIGN SYSTEM ===== */
        :root {
            --bg-primary: #F4F5F7;
            --text-primary: #1a1a2e;
            --text-secondary: #64748b;
            --accent-yellow: #FFD700;
            --accent-yellow-hover: #e6c200;
            --accent-green: #2F3E2F;
            --accent-green-light: rgba(47,62,47,.08);
            --sidebar-bg: #111113;
            --sidebar-text: #a0a0a0;
            --sidebar-hover: rgba(47,62,47,.6);
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --border-light: #f1f3f5;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,.06);
            --shadow-lg: 0 8px 30px rgba(0,0,0,.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: .15s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: 264px;
            background: linear-gradient(180deg, #141416 0%, #111113 40%, #0d0d0f 100%);
            color: var(--sidebar-text);
            z-index: 50;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #333 transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }

        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; gap: .875rem;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent-yellow)  0%, #f5c400 100%);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 1rem; color: #1a1a1a;
            box-shadow: 0 0 20px rgba(255,215,0,.15);
            letter-spacing: -0.5px;
        }
        .sidebar-brand h1 {
            font-size: 1.15rem; font-weight: 800;
            color: #f0f0f0; margin: 0; letter-spacing: .5px;
        }
        .sidebar-brand span {
            font-size: .68rem; color: #555; display: block;
            letter-spacing: .5px; margin-top: 1px;
        }

        .sidebar-nav { padding: .75rem 0; }
        .sidebar-nav a, .sidebar-nav button {
            display: flex; align-items: center; gap: .75rem;
            width: 100%; padding: .7rem 1.25rem .7rem 1.5rem;
            color: var(--sidebar-text); text-decoration: none;
            font-size: .84rem; font-weight: 500;
            border: none; background: none; cursor: pointer;
            transition: all var(--transition);
            border-left: 3px solid transparent;
            position: relative;
        }
        .sidebar-nav a:hover, .sidebar-nav button:hover {
            background: rgba(255,255,255,.04);
            color: #e0e0e0;
            border-left-color: rgba(255,215,0,.3);
        }
        .sidebar-nav a.active {
            background: linear-gradient(90deg, rgba(47,62,47,.5) 0%, rgba(47,62,47,.1) 100%);
            color: var(--accent-yellow);
            border-left-color: var(--accent-yellow);
            font-weight: 600;
        }

        .sidebar-nav .nav-section {
            padding: .75rem 1.5rem .4rem;
            font-size: .62rem; text-transform: uppercase;
            color: #4a4a4a; letter-spacing: 2px;
            margin-top: .75rem; font-weight: 700;
        }
        .sidebar-nav svg { width: 19px; height: 19px; flex-shrink: 0; opacity: .7; }
        .sidebar-nav a.active svg { opacity: 1; }

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: 264px; min-height: 100vh; }

        /* ===== TOPBAR ===== */
        .topbar {
            background: rgba(255,255,255,.82);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: .65rem 2rem;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 40;
            transition: box-shadow .3s;
        }
        .topbar.scrolled { box-shadow: 0 2px 12px rgba(0,0,0,.06); }

        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .topbar-right { display: flex; align-items: center; gap: .75rem; }

        .topbar h2 {
            font-size: 1.05rem; margin: 0; font-weight: 700;
            color: var(--text-primary);
        }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%; background: var(--accent-green);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; text-transform: uppercase;
        }
        .user-badge {
            background: linear-gradient(135deg, var(--accent-green) 0%, #1e2e1e 100%);
            color: #fff; padding: .2rem .7rem; border-radius: 999px;
            font-size: .68rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .5px;
        }
        .user-name {
            font-size: .84rem; font-weight: 600; color: var(--text-primary);
        }

        .content { padding: 1.75rem 2rem 2.5rem; }

        /* ===== MOBILE ===== */
        .menu-toggle {
            display: none; background: none; border: none;
            cursor: pointer; padding: .5rem; border-radius: var(--radius-sm);
            transition: background var(--transition);
        }
        .menu-toggle:hover { background: var(--accent-green-light); }
        .menu-toggle svg { width: 22px; height: 22px; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 45; backdrop-filter: blur(2px); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
            .overlay.open { display: block; }
            .content { padding: 1rem; }
            .topbar { padding: .65rem 1rem; }
        }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: box-shadow var(--transition);
        }
        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem; padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-light);
        }
        .card-header h3 {
            margin: 0; font-size: .95rem; font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.35rem;
            border: 1px solid var(--border-color);
            position: relative; overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition);
        }
        .stat-card::after {
            content: ''; position: absolute; top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: .04; transform: translate(20px, -20px);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        .stat-card .stat-icon {
            width: 42px; height: 42px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: .75rem;
        }
        .stat-card .stat-value {
            font-size: 1.5rem; font-weight: 800; margin: 0;
            letter-spacing: -.5px; color: var(--text-primary);
        }
        .stat-card .stat-label {
            font-size: .78rem; color: var(--text-secondary);
            margin: .3rem 0 0; font-weight: 500;
        }

        .stat-yellow .stat-icon { background: #FFF8E1; color: #B8860B; }
        .stat-yellow::after { background: #FFD700; }
        .stat-green .stat-icon { background: #E8F5E9; color: var(--accent-green); }
        .stat-green::after { background: var(--accent-green); }
        .stat-dark .stat-icon { background: #263238; color: #FFD700; }
        .stat-dark::after { background: #333; }
        .stat-red .stat-icon { background: #FFEBEE; color: #c62828; }
        .stat-red::after { background: #ef4444; }
        .stat-blue .stat-icon { background: #E3F2FD; color: #1565C0; }
        .stat-blue::after { background: #3b82f6; }

        /* ===== TABLES ===== */
        .table-container { overflow-x: auto; border-radius: var(--radius-md); }

        table { width: 100%; border-collapse: collapse; font-size: .84rem; }

        th {
            background: var(--accent-green-light);
            font-weight: 700; text-align: left;
            padding: .8rem 1rem;
            border-bottom: 2px solid var(--border-color);
            font-size: .72rem; text-transform: uppercase;
            color: var(--accent-green); letter-spacing: .5px;
            position: sticky; top: 0; z-index: 1;
        }
        td {
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
            color: var(--text-primary);
        }
        tbody tr { transition: background var(--transition); }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover td { background: var(--accent-green-light); }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.15rem; border-radius: var(--radius-sm);
            font-size: .82rem; font-weight: 600;
            border: none; cursor: pointer; text-decoration: none;
            transition: all var(--transition);
            box-shadow: var(--shadow-sm);
            line-height: 1.4;
        }
        .btn:active { transform: scale(.97); }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-green) 0%, #1e2e1e 100%);
            color: #fff;
        }
        .btn-primary:hover { box-shadow: 0 4px 14px rgba(47,62,47,.3); }

        .btn-warning {
            background: linear-gradient(135deg, var(--accent-yellow) 0%, #f0c800 100%);
            color: #1a1a1a;
        }
        .btn-warning:hover { box-shadow: 0 4px 14px rgba(255,215,0,.35); }

        .btn-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; }
        .btn-danger:hover { box-shadow: 0 4px 14px rgba(239,68,68,.3); }

        .btn-secondary { background: #6b7280; color: #fff; }
        .btn-secondary:hover { background: #4b5563; }

        .btn-sm { padding: .32rem .7rem; font-size: .76rem; }

        .btn-outline {
            background: #fff; border: 1.5px solid var(--border-color);
            color: var(--text-primary); box-shadow: none;
        }
        .btn-outline:hover {
            border-color: var(--accent-green); color: var(--accent-green);
            background: var(--accent-green-light);
        }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block; font-size: .82rem; font-weight: 600;
            margin-bottom: .45rem; color: var(--text-primary);
        }

        .form-control {
            width: 100%; padding: .55rem .85rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: .85rem; font-family: inherit;
            transition: all var(--transition);
            background: #fff; color: var(--text-primary);
        }
        .form-control:focus {
            outline: none; border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(47,62,47,.1);
        }
        .form-control::placeholder { color: #b0b0b0; }

        /* Tom Select Custom Theme */
        .ts-wrapper.single .ts-control {
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: .5rem .85rem; font-size: .85rem;
            background: #fff; cursor: pointer; min-height: 38px;
            transition: all var(--transition);
        }
        .ts-wrapper.single .ts-control:hover { border-color: #bbb; }
        .ts-wrapper.single.focus .ts-control {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(47,62,47,.1);
        }
        .ts-dropdown {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-top: 4px;
            box-shadow: var(--shadow-lg);
            font-size: .85rem; overflow: hidden;
        }
        .ts-dropdown .option { padding: .55rem .85rem; transition: all var(--transition); }
        .ts-dropdown .active { background: var(--accent-green); color: #fff; }
        .ts-dropdown .option:hover { background: var(--accent-green-light); color: var(--text-primary); }
        .ts-dropdown .active:hover { background: var(--accent-green); color: #fff; }
        .ts-wrapper .ts-control > input { font-size: .85rem; }
        .ts-wrapper .ts-control > input::placeholder { color: #aaa; }
        select.form-control { appearance: auto; }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .form-error { color: #ef4444; font-size: .78rem; margin-top: .3rem; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-flex; align-items: center;
            padding: .22rem .6rem; border-radius: 999px;
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .5px;
            border: 1px solid transparent;
        }
        .badge-success { background: #E8F5E9; color: #2e7d32; border-color: #C8E6C9; }
        .badge-danger { background: #FFEBEE; color: #c62828; border-color: #FFCDD2; }
        .badge-warning { background: #FFF8E1; color: #f57f17; border-color: #FFECB3; }
        .badge-info { background: #E3F2FD; color: #1565C0; border-color: #BBDEFB; }
        .badge-secondary { background: #F1F5F9; color: #475569; border-color: #E2E8F0; }

        /* ===== ALERTS ===== */
        .alert {
            padding: .9rem 1.15rem; border-radius: var(--radius-md);
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: .65rem;
            font-size: .85rem; font-weight: 500;
            animation: slideDown .35s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .alert-success {
            background: #E8F5E9; color: #2e7d32;
            border: 1px solid #C8E6C9;
        }
        .alert-danger {
            background: #FFEBEE; color: #c62828;
            border: 1px solid #FFCDD2;
        }
        .alert-close {
            position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            font-size: 1.1rem; color: inherit; opacity: .5;
            transition: opacity var(--transition);
        }
        .alert-close:hover { opacity: 1; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex; list-style: none; padding: 0;
            gap: .3rem; margin-top: 1.25rem; justify-content: center;
        }
        .pagination a, .pagination span {
            padding: .35rem .7rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: .78rem; color: var(--text-secondary);
            text-decoration: none; display: block;
            transition: all var(--transition);
            font-weight: 500;
        }
        .pagination .active span {
            background: var(--accent-green); color: #fff;
            border-color: var(--accent-green);
            box-shadow: 0 2px 8px rgba(47,62,47,.25);
        }
        .pagination a:hover {
            background: var(--accent-green-light);
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* ===== SEARCH ===== */
        .search-bar { display: flex; gap: .5rem; margin-bottom: 1.5rem; }
        .search-bar input { flex: 1; max-width: 380px; }

        /* ===== DETAIL ROWS ===== */
        .detail-row {
            display: flex; padding: .65rem 0;
            border-bottom: 1px solid var(--border-light);
            font-size: .875rem;
        }
        .detail-row dt {
            width: 180px; font-weight: 600;
            color: var(--text-secondary); flex-shrink: 0;
        }
        .detail-row dd { margin: 0; color: var(--text-primary); font-weight: 500; }

        /* ===== PAGE HEADER (NEW) ===== */
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
        }
        .page-header-actions { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }

        /* ===== SECTION TITLE (NEW) ===== */
        .section-title {
            font-size: 1rem; font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex; align-items: center; gap: .5rem;
            padding-bottom: .5rem;
            border-bottom: 2px solid var(--border-light);
        }
        .section-title svg { width: 20px; height: 20px; color: var(--accent-green); }

        /* ===== EMPTY STATE (NEW) ===== */
        .empty-state {
            text-align: center; padding: 3rem 1.5rem;
        }
        .empty-state svg { width: 56px; height: 56px; color: #d1d5db; margin-bottom: 1rem; }
        .empty-state p {
            color: var(--text-secondary); font-size: .9rem;
            margin-bottom: .25rem;
        }
        .empty-state .empty-state-title {
            font-weight: 700; color: var(--text-primary);
            font-size: .95rem; margin-bottom: .25rem;
        }
        .empty-state .btn { margin-top: 1rem; }

        /* ===== ACTIVITY TIMELINE (NEW) ===== */
        .activity-item {
            display: flex; gap: 1rem; padding: .75rem 0;
            position: relative;
        }
        .activity-item:not(:last-child)::after {
            content: ''; position: absolute;
            left: 17px; top: 48px; bottom: -4px;
            width: 2px; background: var(--border-light);
        }
        .activity-icon {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .activity-text { font-weight: 600; color: var(--text-primary); font-size: .88rem; }
        .activity-time { font-size: .73rem; color: var(--text-secondary); margin-top: 2px; }

        @stack('styles')
    </style>
</head>

<body>
    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">LAF</div>
            <div>
                <h1>LAF</h1>
                <span>Inventory System</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('produk.index') }}" class="{{ request()->routeIs('produk.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Master Produk
            </a>

            <div class="nav-section">Transaksi</div>
            <a href="{{ route('barang-masuk.index') }}"
                class="{{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Barang Masuk
            </a>
            <a href="{{ route('barang-masuk.riwayat') }}" class="{{ request()->routeIs('barang-masuk.riwayat') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat Masuk
            </a>
            <a href="{{ route('penjualan.index') }}" class="{{ request()->routeIs('penjualan.index') || request()->routeIs('penjualan.create') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Penjualan (Kasir)
            </a>
            <a href="{{ route('penjualan.import-shopee') }}" class="{{ request()->routeIs('penjualan.import-shopee') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Shopee Excel
            </a>
            <a href="{{ route('purchase-order.index') }}"
                class="{{ request()->routeIs('purchase-order.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Purchase Order
            </a>

            <a href="{{ route('riwayat-gudang') }}" class="{{ request()->routeIs('riwayat-gudang') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
                Riwayat Gudang
            </a>
            <a href="{{ route('riwayat-tanggal') }}" class="{{ request()->routeIs('riwayat-tanggal') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Riwayat / Tanggal
            </a>
            <a href="{{ route('riwayat-supplier-pelanggan') }}" class="{{ request()->routeIs('riwayat-supplier-pelanggan') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Supplier & Pelanggan
            </a>
            <a href="{{ route('arus-barang') }}" class="{{ request()->routeIs('arus-barang') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Arus Barang
            </a>

            <div class="nav-section">Analisis</div>
            <a href="{{ route('fp-growth.index') }}" class="{{ request()->routeIs('fp-growth.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                FP-Growth (Promo)
            </a>

            <div class="nav-section">Monitoring</div>
            <a href="{{ route('stok-minimum.index') }}"
                class="{{ request()->routeIs('stok-minimum.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                Stok Minimum
            </a>
            <a href="{{ route('stok-lokasi.index') }}" class="{{ request()->routeIs('stok-lokasi.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Stok per Gudang
            </a>
            <a href="{{ route('laporan.nilai-aset') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Laporan Nilai Aset
            </a>

            @if(auth()->user()->role === 'admin')
                <div class="nav-section">Master Data</div>
                <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Kategori
                </a>
                <a href="{{ route('lokasi.index') }}" class="{{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Lokasi
                </a>
                <a href="{{ route('supplier.index') }}" class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Supplier
                </a>
                <a href="{{ route('pelanggan.index') }}" class="{{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Pelanggan
                </a>
                <a href="{{ route('satuan.index') }}" class="{{ request()->routeIs('satuan.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    Satuan
                </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar" id="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2>{{ $title ?? 'Dashboard' }}</h2>
            </div>
            <div class="topbar-right">
                <span class="user-badge">{{ auth()->user()->role }}</span>
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                </form>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success" id="alertSuccess">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                    <button class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" id="alertError">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                    <button class="alert-close" onclick="this.parentElement.remove()" aria-label="Close">✕</button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('open');
        }
        // Topbar scroll shadow
        window.addEventListener('scroll', function() {
            document.getElementById('topbar').classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });
        // Alert auto-dismiss
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.alert').forEach(function(el) {
                setTimeout(function() {
                    el.style.transition = 'opacity .4s, transform .4s';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(function() { el.remove(); }, 400);
                }, 5000);
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Auto-init Tom Select on all elements with class 'tom-select'
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select.tom-select').forEach(function(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: el.dataset.placeholder || 'Ketik untuk mencari...',
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>

