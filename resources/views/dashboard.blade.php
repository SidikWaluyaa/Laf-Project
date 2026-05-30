<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    @if(session('success'))
        <div style="background:#d1fae5; color:#065f46; padding:1rem; border-radius:8px; margin-bottom:1.5rem; border:1px solid #34d399; font-weight:500;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2; color:#991b1b; padding:1rem; border-radius:8px; margin-bottom:1.5rem; border:1px solid #f87171; font-weight:500;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <p style="color:var(--text-secondary); margin:0;">Gudang Analytics & Reporting</p>
        </div>
        <div>
            <form action="{{ route('tutup-shift') }}" method="POST" onsubmit="return confirm('Tutup shift hari ini dan kirim Laporan Rekap ke WhatsApp Owner?');">
                @csrf
                <button type="submit" class="btn btn-primary" style="display:flex; align-items:center; gap:.5rem; background:#10b981; border:none; border-radius:6px; padding:.6rem 1.2rem; color:#fff; font-weight:600; cursor:pointer; box-shadow:0 4px 6px -1px rgba(16, 185, 129, 0.2);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Tutup Shift & Kirim Laporan WA
                </button>
            </form>
        </div>
    </div>

    {{-- SECTION 1: OVERVIEW STATS --}}
    <div style="margin-bottom:2rem;">
        <h2 class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Overview
        </h2>
        <div class="stats-grid">
            <div class="stat-card stat-yellow">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <p class="stat-value">{{ number_format($totalProduk) }}</p>
                <p class="stat-label">Total Produk</p>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                </div>
                <p class="stat-value">{{ number_format($totalStok) }}</p>
                <p class="stat-label">Total Stok</p>
            </div>
            <div class="stat-card stat-dark">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="stat-value">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</p>
                <p class="stat-label">Total Nilai Aset</p>
            </div>
            <div class="stat-card stat-red">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <p class="stat-value">{{ $lowStockProducts->count() }}</p>
                <p class="stat-label">Barang Stok Minimum</p>
            </div>
            <div class="stat-card stat-blue">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="stat-value">{{ number_format($pendingPOCount) }}</p>
                <p class="stat-label">PO Belum Selesai</p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: MAIN CHARTS --}}
    <div style="margin-bottom:2rem;">
        <h2 class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Analisis Performa
        </h2>
        <div style="display:grid;grid-template-columns:3fr 2fr;gap:1.5rem;">
            <div class="card" style="margin-bottom:0;display:flex;flex-direction:column;">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h3>Arus Barang ({{ $currentYear }})</h3>
                    <select onchange="window.location.href='?year='+this.value" style="padding:.25rem .5rem;border:1px solid var(--border-light);border-radius:6px;font-size:.8rem;background:#fff;">
                        @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div style="padding:.5rem 0;flex:1;min-height:280px;">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
            
            <div class="card" style="margin-bottom:0;display:flex;flex-direction:column;">
                <div class="card-header">
                    <h3>Distribusi Nilai Aset per Kategori</h3>
                </div>
                <div style="padding:.5rem 0;flex:1;display:flex;align-items:center;justify-content:center;min-height:280px;">
                    @if(count($assetCategoryLabels) > 0)
                        <canvas id="assetCategoryChart"></canvas>
                    @else
                        <div class="empty-state" style="padding:1.5rem;">
                            <p>Belum ada data nilai aset</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: TOP PRODUCTS & ACTIVITY --}}
    <div style="margin-bottom:2rem;">
        <h2 class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Pergerakan Terkini
        </h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            <div class="card" style="margin-bottom:0;display:flex;flex-direction:column;">
                <div class="card-header">
                    <h3>🔥 Top 5 Produk Terlaris (30 Hari)</h3>
                </div>
                <div style="padding:.5rem 0;flex:1;min-height:250px;">
                    @if(count($topProducts) > 0)
                        <canvas id="topProductsChart"></canvas>
                    @else
                        <div class="empty-state" style="padding:1.5rem;">
                            <p>Belum ada data penjualan 30 hari terakhir.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <h3>🕒 Aktivitas Gudang Terkini</h3>
                </div>
                <div style="padding:.25rem 0;">
                    @forelse($recentActivity as $act)
                    <div class="activity-item">
                        <div class="activity-icon" style="background:{{ $act->color }}18;color:{{ $act->color }};">
                            {{ $act->icon }}
                        </div>
                        <div>
                            <div class="activity-text">{{ $act->deskripsi }}</div>
                            <div class="activity-time">{{ \Carbon\Carbon::parse($act->waktu)->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                        <div class="empty-state" style="padding:1.5rem;">
                            <p>Belum ada aktivitas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 4: WARNINGS (LOW STOCK & DEAD STOCK) --}}
    <div style="margin-bottom:2rem;">
        <h2 class="section-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            Peringatan Gudang
        </h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
            {{-- Low Stock --}}
            <div class="card" style="margin-bottom:0;border-top:3px solid #ef4444;">
                <div class="card-header">
                    <h3 style="color:#ef4444;">⚠️ Barang Stok Minimum</h3>
                </div>
                <div class="table-container" style="max-height:300px;overflow-y:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th style="text-align:center;">Stok/Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts->take(5) as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->kode_barang }}</strong><br>
                                    <span style="font-size:.78rem;color:var(--text-secondary);">{{ $p->nama_barang }}</span>
                                </td>
                                <td style="text-align:center;color:#ef4444;font-weight:700;">
                                    {{ $p->stokProduk->sum('total_stok') }} <span style="color:var(--text-secondary);font-weight:normal;font-size:.78rem;">/ {{ $p->stokMinimum->stok_minimum ?? '-' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="empty-state" style="padding:1.5rem;">Semua stok aman. ✅</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($lowStockProducts->count() > 5)
                        <div style="text-align:center;padding:.75rem;background:var(--accent-green-light);border-top:1px solid var(--border-light);">
                            <a href="{{ route('stok-minimum.index') }}" style="font-size:.8rem;color:var(--accent-green);font-weight:600;">Lihat Semua ({{ $lowStockProducts->count() }}) &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dead Stock --}}
            <div class="card" style="margin-bottom:0;border-top:3px solid #f59e0b;">
                <div class="card-header">
                    <h3 style="color:#d97706;">💤 Dead Stock (Tidak Keluar > 60 Hari)</h3>
                </div>
                <div class="table-container" style="max-height:300px;overflow-y:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th style="text-align:center;">Total Stok Mandek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deadStock as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->kode_barang }}</strong><br>
                                    <span style="font-size:.78rem;color:var(--text-secondary);">{{ $p->nama_barang }}</span>
                                </td>
                                <td style="text-align:center;color:#d97706;font-weight:700;">
                                    {{ number_format($p->total_stok) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="empty-state" style="padding:1.5rem;">Tidak ada dead stock. Excellent! 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns:3fr 2fr"],
            div[style*="grid-template-columns:1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function initDashboardCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initDashboardCharts, 300);
                return;
            }

            var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            var masukData = @json($barangMasukChart);
            var keluarData = @json($barangKeluarChart);
            var assetLabels = @json($assetCategoryLabels);
            var assetData = @json($assetCategoryData);
            var topLabels = @json(collect($topProducts)->pluck('nama_barang'));
            var topData = @json(collect($topProducts)->pluck('total_keluar'));

            // 1. Arus Barang (Bar Chart)
            var el1 = document.getElementById('stockChart');
            if (el1) {
                new Chart(el1.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            { label: 'Barang Masuk', data: months.map(function(_, i) { return masukData[i + 1] || 0; }), backgroundColor: '#10b981', borderRadius: 4 },
                            { label: 'Barang Keluar', data: months.map(function(_, i) { return keluarData[i + 1] || 0; }), backgroundColor: '#3b82f6', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false } }
                        }
                    }
                });
            }

            // 2. Distribusi Nilai Aset (Doughnut)
            var el2 = document.getElementById('assetCategoryChart');
            if (el2 && assetLabels.length > 0) {
                var assetColors = ['#f59e0b', '#06b6d4', '#8b5cf6', '#ec4899', '#10b981', '#3b82f6', '#64748b'];
                new Chart(el2.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: assetLabels,
                        datasets: [{ data: assetData, backgroundColor: assetColors, borderWidth: 0, hoverOffset: 4 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '65%',
                        layout: { padding: 10 },
                        plugins: {
                            legend: { position: 'right', labels: { boxWidth: 10, padding: 15, usePointStyle: true, font: { size: 11 } } },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var lbl = context.label || '';
                                        if (lbl) lbl += ': ';
                                        lbl += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.raw);
                                        return lbl;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 3. Top 5 Produk Terlaris (Horizontal Bar)
            var el3 = document.getElementById('topProductsChart');
            if (el3 && topLabels.length > 0) {
                new Chart(el3.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: topLabels,
                        datasets: [{ label: 'Terjual/Keluar', data: topData, backgroundColor: '#f59e0b', borderRadius: 4 }]
                    },
                    options: {
                        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' }, border: { display: false } },
                            y: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }
        }
        initDashboardCharts();
    </script>
    @endpush
</x-app-layout>
