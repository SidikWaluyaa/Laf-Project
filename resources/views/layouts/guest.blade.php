<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login — LAF Inventory</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="dns-prefetch" href="https://fonts.bunny.net">
        <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" /></noscript>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                -webkit-font-smoothing: antialiased;
                min-height: 100vh;
            }
            .login-wrapper {
                display: flex; min-height: 100vh;
            }
            /* Left Panel — Branding */
            .login-brand {
                flex: 1; display: flex; flex-direction: column;
                justify-content: center; align-items: center;
                background: linear-gradient(135deg, #111113 0%, #1a1a1a 50%, #2F3E2F 100%);
                color: #fff; padding: 3rem; position: relative; overflow: hidden;
            }
            .login-brand::before {
                content: ''; position: absolute; inset: 0;
                background: radial-gradient(circle at 30% 70%, rgba(255,215,0,.08) 0%, transparent 50%),
                            radial-gradient(circle at 70% 30%, rgba(47,62,47,.2) 0%, transparent 50%);
            }
            .login-brand-content { position: relative; z-index: 1; text-align: center; max-width: 400px; }
            .login-brand-icon {
                width: 72px; height: 72px;
                background: linear-gradient(135deg, #FFD700 0%, #f5c400 100%);
                border-radius: 16px; display: flex; align-items: center; justify-content: center;
                font-weight: 900; font-size: 1.5rem; color: #1a1a1a;
                margin: 0 auto 1.5rem;
                box-shadow: 0 0 40px rgba(255,215,0,.2);
            }
            .login-brand h1 {
                font-size: 2rem; font-weight: 800;
                letter-spacing: 1px; margin-bottom: .5rem;
            }
            .login-brand p {
                color: rgba(255,255,255,.5); font-size: .9rem;
                line-height: 1.6; margin-bottom: 2rem;
            }
            .login-brand-features {
                display: flex; flex-direction: column; gap: .75rem;
                text-align: left;
            }
            .login-brand-features div {
                display: flex; align-items: center; gap: .75rem;
                color: rgba(255,255,255,.6); font-size: .84rem;
            }
            .login-brand-features .feature-dot {
                width: 8px; height: 8px; border-radius: 50%;
                background: #FFD700; flex-shrink: 0;
            }

            /* Right Panel — Form */
            .login-form-panel {
                flex: 1; display: flex; flex-direction: column;
                justify-content: center; align-items: center;
                padding: 3rem; background: #F4F5F7;
                min-height: 100vh;
            }
            .login-form-container {
                width: 100%; max-width: 400px;
            }
            .login-form-header {
                margin-bottom: 2rem;
            }
            .login-form-header h2 {
                font-size: 1.5rem; font-weight: 800;
                color: #1a1a2e; margin-bottom: .35rem;
            }
            .login-form-header p {
                color: #64748b; font-size: .88rem;
            }
            .login-card {
                background: #fff; border-radius: 16px;
                padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,.04);
                border: 1px solid #e5e7eb;
            }
            .login-card .form-group { margin-bottom: 1.25rem; }
            .login-card label {
                display: block; font-size: .82rem; font-weight: 600;
                margin-bottom: .45rem; color: #1a1a2e;
            }
            .login-card input[type="email"],
            .login-card input[type="password"] {
                width: 100%; padding: .65rem .85rem;
                border: 1.5px solid #e5e7eb; border-radius: 8px;
                font-size: .88rem; font-family: inherit;
                transition: all .15s ease; background: #fff; color: #1a1a2e;
            }
            .login-card input:focus {
                outline: none; border-color: #2F3E2F;
                box-shadow: 0 0 0 3px rgba(47,62,47,.1);
            }
            .login-card input::placeholder { color: #b0b0b0; }
            .login-error { color: #ef4444; font-size: .78rem; margin-top: .3rem; }

            .remember-row {
                display: flex; align-items: center; gap: .5rem;
                margin-bottom: 1.5rem;
            }
            .remember-row input[type="checkbox"] {
                width: 16px; height: 16px; border-radius: 4px;
                accent-color: #2F3E2F;
            }
            .remember-row label {
                font-size: .84rem; color: #64748b; margin: 0;
                font-weight: 500; cursor: pointer;
            }

            .login-actions {
                display: flex; align-items: center; justify-content: space-between;
                gap: 1rem;
            }
            .login-actions a {
                font-size: .82rem; color: #64748b;
                text-decoration: none; transition: color .15s;
            }
            .login-actions a:hover { color: #2F3E2F; }

            .btn-login {
                padding: .6rem 1.75rem; font-size: .88rem; font-weight: 700;
                background: linear-gradient(135deg, #2F3E2F 0%, #1e2e1e 100%);
                color: #fff; border: none; border-radius: 8px;
                cursor: pointer; transition: all .15s ease;
                box-shadow: 0 1px 3px rgba(0,0,0,.1);
            }
            .btn-login:hover {
                box-shadow: 0 4px 14px rgba(47,62,47,.3);
                transform: translateY(-1px);
            }
            .btn-login:active { transform: scale(.98); }

            .login-footer {
                text-align: center; margin-top: 1.5rem;
                font-size: .78rem; color: #94a3b8;
            }

            @media (max-width: 768px) {
                .login-brand { display: none; }
                .login-form-panel { padding: 1.5rem; }
            }
        </style>
    </head>
    <body>
        <div class="login-wrapper">
            <!-- Left: Branding Panel -->
            <div class="login-brand">
                <div class="login-brand-content">
                    <div class="login-brand-icon">LAF</div>
                    <h1>LAF Inventory</h1>
                    <p>Sistem manajemen inventaris modern untuk mengelola stok, transaksi, dan laporan bisnis Anda.</p>
                    <div class="login-brand-features">
                        <div><span class="feature-dot"></span> Monitoring stok real-time</div>
                        <div><span class="feature-dot"></span> Manajemen barang masuk & penjualan</div>
                        <div><span class="feature-dot"></span> Laporan nilai aset & analytics</div>
                        <div><span class="feature-dot"></span> Multi-lokasi & multi-user</div>
                    </div>
                </div>
            </div>

            <!-- Right: Login Form -->
            <div class="login-form-panel">
                <div class="login-form-container">
                    <div class="login-form-header">
                        <h2>Selamat Datang 👋</h2>
                        <p>Masuk ke akun Anda untuk melanjutkan</p>
                    </div>
                    {{ $slot }}
                    <div class="login-footer">
                        &copy; {{ date('Y') }} LAF Inventory System
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
