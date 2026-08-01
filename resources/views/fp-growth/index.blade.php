<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <div class="page-header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Analisis Rekomendasi Paket Promo (FP-Growth)</h2>
            <p class="text-sm text-gray-500">Menggunakan Algoritma FP-Growth untuk menemukan pola kombinasi pembelian konsumen.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('penjualan.import-shopee') }}" class="btn btn-warning">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Transaksi Shopee
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Panel Parameter -->
        <div class="md:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h3>Parameter Analisis</h3>
                </div>
                <form action="{{ route('fp-growth.process') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="sumber_data">Sumber Data Transaksi</label>
                        <select name="sumber_data" id="sumber_data" class="form-control" required>
                            <option value="shopee" {{ old('sumber_data', $selectedSumberData ?? 'shopee') === 'shopee' ? 'selected' : '' }}>🟧 Shopee Marketplace (Data Import)</option>
                            <option value="kasir" {{ old('sumber_data', $selectedSumberData ?? '') === 'kasir' ? 'selected' : '' }}>🛒 Penjualan Kasir (POS Offline)</option>
                            <option value="semua" {{ old('sumber_data', $selectedSumberData ?? '') === 'semua' ? 'selected' : '' }}>🔀 Gabungan (Semua Transaksi)</option>
                        </select>
                        <small class="text-gray-400">Pilih asal data transaksi yang ingin dianalisis.</small>
                    </div>

                    <div class="form-group">
                        <label for="group_by">Tingkat Granularitas Produk</label>
                        <select name="group_by" id="group_by" class="form-control" required>
                            <option value="parent_sku" {{ old('group_by', $groupBy ?? 'parent_sku') === 'parent_sku' ? 'selected' : '' }}>🏷️ Produk Utama / SKU Induk (Rekomendasi)</option>
                            <option value="full_name" {{ old('group_by', $groupBy ?? '') === 'full_name' ? 'selected' : '' }}>🔍 Nama Produk + Variasi Lengkap</option>
                        </select>
                        <small class="text-gray-400">Gunakan Produk Utama agar frekuensi variasi warna/ukuran berkumpul solid.</small>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $startDate ?? date('Y-05-01')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $endDate ?? date('Y-05-31')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="min_support">Minimum Support (%)</label>
                        <input type="number" name="min_support" id="min_support" class="form-control" value="{{ old('min_support', $minSupport ?? '0.05') }}" step="0.01" min="0.01" max="100" required>
                        <small class="text-gray-400">Persentase minimal kemunculan produk dalam seluruh keranjang.</small>
                    </div>

                    <div class="form-group">
                        <label for="min_confidence">Minimum Confidence (%)</label>
                        <input type="number" name="min_confidence" id="min_confidence" class="form-control" value="{{ old('min_confidence', $minConfidence ?? '10') }}" step="0.1" min="0.1" max="100" required>
                        <small class="text-gray-400">Kuatnya hubungan antar produk dalam satu paket promo.</small>
                    </div>

                    <div class="form-group p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <label class="flex items-center gap-2 cursor-pointer mb-0 font-semibold text-xs text-gray-700">
                            <input type="checkbox" name="abaikan_packing" value="1" {{ old('abaikan_packing', $abaikanPacking ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600">
                            <span>Abaikan Item Packing &amp; Kelengkapan</span>
                        </label>
                        <small class="text-gray-400 text-xs block mt-1">Mengabaikan item non-produk seperti Bubble Wrap, Box Kardus, Extra Packing, dll.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center mt-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Mulai Analisis FP-Growth
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Hasil -->
        <div class="md:col-span-2">
            @if(count($results) > 0 && !empty($topRecommendations))
                <!-- Executive Summary Card -->
                <div class="bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 rounded-2xl p-5 text-white shadow-xl mb-6 border border-blue-800">
                    <div class="flex items-center justify-between pb-3 border-b border-blue-700/60 mb-4">
                        <h3 class="font-bold text-base flex items-center gap-2 text-yellow-300">
                            <span class="w-8 h-8 rounded-lg bg-yellow-400/20 text-yellow-300 flex items-center justify-center flex-shrink-0 text-base">💡</span>
                            Ringkasan Kesimpulan Rekomendasi Promo Bundling Terbaik
                        </h3>
                        <span class="bg-yellow-400 text-blue-950 font-extrabold px-2.5 py-1 rounded-full text-3xs">
                            {{ count($topRecommendations) }} Rekomendasi Utama
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @foreach($topRecommendations as $idx => $top)
                            <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/15 hover:bg-white/15 transition-all">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                    <span class="font-bold text-xs text-yellow-300 uppercase flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-yellow-400 text-blue-950 flex items-center justify-center font-extrabold text-3xs">{{ $idx + 1 }}</span>
                                        {{ $top['kategori'] }}
                                    </span>
                                    <div class="flex items-center gap-2 text-3xs">
                                        <span class="bg-blue-800/80 px-2 py-0.5 rounded text-blue-100">Confidence: <strong>{{ $top['confidence'] }}%</strong></span>
                                        <span class="bg-emerald-800/80 px-2 py-0.5 rounded text-emerald-100">Lift Ratio: <strong>{{ $top['lift_ratio'] }}</strong></span>
                                    </div>
                                </div>

                                <div class="text-xs font-semibold text-white mb-2 flex items-center gap-2 flex-wrap">
                                    <span class="bg-blue-500/30 px-2 py-1 rounded text-blue-100 border border-blue-400/30">{{ $top['ante'] }}</span>
                                    <span class="text-yellow-400 font-bold">&plus;</span>
                                    <span class="bg-emerald-500/30 px-2 py-1 rounded text-emerald-100 border border-emerald-400/30">{{ $top['cons'] }}</span>
                                    <span class="text-gray-300 text-3xs font-normal">({{ $top['count_both'] }} transaksi dibeli bersama)</span>
                                </div>

                                <div class="text-3xs text-blue-100 bg-black/25 p-2 rounded-lg flex items-start gap-1.5 border border-white/5">
                                    <span class="font-bold text-yellow-300 flex-shrink-0">🎯 Saran Aksi Bisnis:</span>
                                    <span>{{ $top['saran_aksi'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card h-full">
                <div class="card-header">
                    <h3>Daftar Aturan Asosiasi (Association Rules Detail)</h3>
                </div>
                
                @if(count($results) === 0)
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="empty-state-title">Belum Ada Data Hasil Analisis</div>
                        <p>Silakan tentukan sumber data &amp; parameter di samping, lalu klik "Mulai Analisis FP-Growth" untuk melihat rekomendasi paket promo.</p>
                    </div>
                @else
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jika Membeli (Antecedent)</th>
                                    <th>Maka Juga Membeli (Consequent)</th>
                                    <th>Support (%)</th>
                                    <th>Confidence (%)</th>
                                    <th>Lift Ratio</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $res)
                                <tr>
                                    <td><span class="badge badge-info">{{ $res['ante'] }}</span></td>
                                    <td><span class="badge badge-success">{{ $res['cons'] }}</span></td>
                                    <td>{{ $res['support'] }}%</td>
                                    <td><strong>{{ $res['confidence'] }}%</strong></td>
                                    <td>
                                        @if(($res['lift_ratio'] ?? 0) >= 1.2)
                                            <span class="badge badge-success font-bold" title="Lift Ratio > 1.2: Hubungan Asosiasi Sangat Valid &amp; Kuat">{{ $res['lift_ratio'] }} (Kuat)</span>
                                        @elseif(($res['lift_ratio'] ?? 0) >= 1.0)
                                            <span class="badge badge-info font-bold" title="Lift Ratio 1.0 - 1.2: Hubungan Netral / Kebetulan">{{ $res['lift_ratio'] }} (Netral)</span>
                                        @else
                                            <span class="badge badge-warning font-bold" title="Lift Ratio < 1.0: Hubungan Kanibalisasi / Substitusi">{{ $res['lift_ratio'] }} (Lemah)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-outline btn-sm" onclick="toggleDetail('detail-{{ $index }}')">
                                                Detail
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="detail-{{ $index }}" style="display: none; background: #fdfdfd;">
                                    <td colspan="6" class="p-4 border-l-4 border-blue-400">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                             <div>
                                                <h5 class="text-xs font-bold text-blue-800 uppercase mb-2 flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Interpretasi Kalimat &amp; Rasio Pembeli:
                                                </h5>
                                                <div class="text-xs text-gray-700 space-y-1.5 leading-relaxed bg-blue-50/40 p-3 rounded border border-blue-100">
                                                    <p>
                                                        &bull; Dari total <strong>{{ number_format($res['total_transactions']) }}</strong> transaksi, terdapat <strong>{{ number_format($res['count_ante']) }}</strong> pembeli yang membeli <strong>{{ $res['ante'] }}</strong>.
                                                    </p>
                                                    <p>
                                                        &bull; Dari {{ number_format($res['count_ante']) }} pembeli tersebut, terdapat <strong>{{ number_format($res['count_both']) }}</strong> pembeli yang <em>juga ikut membeli</em> <strong>{{ $res['cons'] }}</strong> secara bersamaan.
                                                    </p>
                                                    <p class="pt-1.5 text-blue-900 border-t border-blue-100 font-medium">
                                                        🎯 <strong>Rasio Confidence ({{ $res['confidence'] }}%):</strong> Sebanyak <strong>{{ $res['confidence'] }}%</strong> (yaitu {{ $res['count_both'] }} dari {{ $res['count_ante'] }} pembeli {{ $res['ante'] }}) terdorong untuk juga membeli {{ $res['cons'] }}.
                                                    </p>
                                                </div>
                                             </div>
                                            <div>
                                                <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Simulasi Perhitungan (Matematis):</h5>
                                                <div class="text-xs font-mono bg-white p-3 rounded border border-blue-100 space-y-2">
                                                    <div>
                                                        <strong>Support:</strong> (Kemunculan Bersama / Total Transaksi)<br>
                                                        = {{ $res['count_both'] }} / {{ $res['total_transactions'] }}<br>
                                                        = <strong>{{ $res['support'] }}%</strong>
                                                    </div>
                                                    <div>
                                                        <strong>Confidence:</strong> (Kemunculan Bersama / Kemunculan {{ $res['ante'] }})<br>
                                                        = {{ $res['count_both'] }} / {{ $res['count_ante'] }}<br>
                                                        = <strong>{{ $res['confidence'] }}%</strong>
                                                    </div>
                                                    <div class="pt-1.5 border-t border-gray-100 text-blue-900 font-semibold">
                                                        <strong>Lift Ratio:</strong> (Confidence / Support Consequent)<br>
                                                        = {{ $res['confidence'] }}% / ({{ $res['count_cons'] }} / {{ $res['total_transactions'] }})<br>
                                                        = {{ $res['confidence'] }}% / {{ round(($res['count_cons'] / $res['total_transactions']) * 100, 2) }}%<br>
                                                        = <strong>{{ $res['lift_ratio'] }}</strong>
                                                        @if(($res['lift_ratio'] ?? 0) >= 1.2)
                                                            <span class="text-green-600 ml-1">(Aturan Sangat Kuat &amp; Valid)</span>
                                                        @else
                                                            <span class="text-yellow-600 ml-1">(Aturan Netral / Kebetulan)</span>
                                                        @endif
                                                        <div class="text-3xs text-gray-500 font-normal mt-1">
                                                            *Support Consequent ({{ $res['cons'] }}) = {{ $res['count_cons'] }} dari {{ number_format($res['total_transactions']) }} total transaksi toko.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bukti Notasi Transaksi Riil -->
                                        @if(!empty($res['sample_orders']))
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h5 class="text-xs font-bold text-gray-800 uppercase flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Bukti {{ count($res['sample_orders']) }} Transaksi Riil (Pembelian Bersama):
                                                    </h5>
                                                    <span class="text-3xs text-gray-500">Menampilkan sampel bukti nota transaksi</span>
                                                </div>
                                                <div class="bg-white rounded border border-gray-200 overflow-x-auto">
                                                    <table class="w-full text-xs">
                                                        <thead class="bg-gray-50 text-gray-700">
                                                            <tr>
                                                                <th class="p-2 border-b text-left">No. Pesanan</th>
                                                                <th class="p-2 border-b text-left">Waktu Pesanan</th>
                                                                <th class="p-2 border-b text-left">Username Pembeli</th>
                                                                <th class="p-2 border-b text-left">Kota / Lokasi</th>
                                                                <th class="p-2 border-b text-center">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($res['sample_orders'] as $sample)
                                                                <tr class="border-b border-gray-100 hover:bg-blue-50/50">
                                                                    <td class="p-2 font-mono font-bold text-gray-800">{{ $sample['no_pesanan'] }}</td>
                                                                    <td class="p-2 text-gray-600">{{ $sample['waktu'] }}</td>
                                                                    <td class="p-2 font-semibold text-gray-800">{{ $sample['username'] }}</td>
                                                                    <td class="p-2 text-gray-600">{{ $sample['kota'] }}</td>
                                                                    <td class="p-2 text-center">
                                                                        @if(($sample['sumber'] ?? 'Shopee') === 'Shopee')
                                                                            <a href="{{ route('penjualan.import-shopee', ['search' => $sample['no_pesanan']]) }}" target="_blank" class="btn btn-outline btn-sm py-0.5 px-2 text-3xs hover:bg-blue-600 hover:text-white" title="Buka Detail Nota Shopee">
                                                                                Lihat Nota Shopee &rarr;
                                                                            </a>
                                                                        @else
                                                                            <span class="badge badge-secondary py-0.5 px-1 text-3xs">POS Kasir</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                        <h4 class="text-sm font-bold text-yellow-800 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Rekomendasi Strategi Promo Murni Produk:
                        </h4>
                        <p class="text-xs text-yellow-700">
                            Sistem mendeteksi pola kombinasi produk murni di atas dari transaksi yang di-import. 
                            Gunakan data ini untuk membuat paket bundling promo Shopee / Display Produk bersama di area toko.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .bg-yellow-50 { background-color: #fffbeb; }
        .border-yellow-100 { border-color: #fef3c7; }
        .text-yellow-800 { color: #92400e; }
        .text-yellow-700 { color: #b45309; }
    </style>
    @endpush
    @push('scripts')
    <script>
        function toggleDetail(id) {
            const el = document.getElementById(id);
            if (el.style.display === 'none') {
                el.style.display = 'table-row';
            } else {
                el.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>
