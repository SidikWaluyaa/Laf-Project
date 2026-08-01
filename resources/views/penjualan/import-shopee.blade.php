<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <div class="page-header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Import &amp; Data Transaksi Shopee</h2>
            <p class="text-sm text-gray-500">Upload dan kelola data transaksi Shopee Marketplace sebagai sumber data analisis FP-Growth.</p>
        </div>
        <div class="page-header-actions flex gap-2">
            @if($totalOrders > 0)
                <button type="button" class="btn btn-danger" onclick="openModalHapusMassal()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Data Massal
                </button>
            @endif
            <a href="{{ route('fp-growth.index') }}" class="btn btn-warning">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Ke Analisis FP-Growth
            </a>
        </div>
    </div>

    <!-- Stats Ringkasan Data Shopee -->
    <div class="stats-grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card stat-yellow">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($totalOrders) }}</div>
            <div class="stat-label">Total Pesanan (Nota Unik)</div>
        </div>

        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($totalDetails) }}</div>
            <div class="stat-label">Total Item Produk (Baris Excel)</div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($totalSelesai) }}</div>
            <div class="stat-label">Pesanan Berstatus Selesai</div>
        </div>

        <div class="stat-card stat-dark">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
            </div>
            <div class="stat-value">{{ number_format($totalMultiItemOrders) }}</div>
            <div class="stat-label">Pesanan Kombinasi (&ge; 2 Produk)</div>
        </div>
    </div>

    <!-- Section Upload File Excel -->
    <div class="card mb-6 overflow-hidden border border-gray-200/80 shadow-xs">
        <div class="card-header bg-gray-50/80 border-b border-gray-100 py-3.5 px-5">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </span>
                Upload File Laporan Excel Shopee Baru
            </h3>
        </div>
        <div class="p-5 bg-white">
            <form action="{{ route('penjualan.import-shopee.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col md:flex-row items-stretch gap-4">
                    <div class="flex-1 w-full">
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-3.5 transition-all hover:border-orange-400 bg-gray-50/50 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base flex-shrink-0">
                                📊
                            </div>
                            <div class="flex-1 min-w-0">
                                <label for="file_excel" class="block font-semibold text-xs text-gray-800 mb-1">
                                    Pilih File Excel Transaksi (.xlsx / .xls)
                                </label>
                                <input type="file" name="file_excel" id="file_excel" class="form-control text-xs py-1 px-2 bg-white" accept=".xlsx, .xls" required>
                                <small class="text-3xs text-gray-500 block mt-1">Format resmi: <code class="bg-gray-200 px-1 py-0.5 rounded text-gray-700">Order.all.YYYYMMDD_YYYYMMDD.xlsx</code> dari Shopee Seller Centre.</small>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-auto flex items-center">
                        <button type="submit" class="btn btn-primary px-6 py-3 w-full md:w-auto justify-center font-bold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 border-none text-white h-full min-h-[50px]">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Proses Import Excel</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Section Tabel Data Transaksi Shopee -->
    <div class="card">
        <div class="card-header border-b-0 pb-0 mb-3">
            <div>
                <h3>Daftar Transaksi Shopee (Keranjang Belanja FP-Growth)</h3>
                <p class="text-xs text-gray-500 mt-1">Lihat produk-produk yang dibeli bersamaan pada setiap Nomor Pesanan Shopee.</p>
            </div>
        </div>

        <!-- Filter Tab Buttons -->
        <div class="flex border-b border-gray-200 mb-4 px-2 gap-2 overflow-x-auto">
            <a href="{{ route('penjualan.import-shopee', array_merge(request()->query(), ['filter_basket' => 'all', 'page' => 1])) }}"
               class="px-4 py-2 text-xs font-bold rounded-t-lg transition-all border-b-2 {{ ($filterBasket ?? 'all') === 'all' ? 'border-yellow-500 text-gray-900 bg-yellow-50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
               Semua Pesanan ({{ number_format($totalOrders) }})
            </a>
            <a href="{{ route('penjualan.import-shopee', array_merge(request()->query(), ['filter_basket' => 'multi_item', 'page' => 1])) }}"
               class="px-4 py-2 text-xs font-bold rounded-t-lg transition-all border-b-2 flex items-center gap-1 {{ ($filterBasket ?? '') === 'multi_item' ? 'border-blue-600 text-blue-800 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
               <span>🛒 Pesanan Kombinasi (&ge; 2 Produk)</span>
               <span class="badge badge-info ml-1">{{ number_format($totalMultiItemOrders) }}</span>
            </a>
            <a href="{{ route('penjualan.import-shopee', array_merge(request()->query(), ['filter_basket' => 'repeat_customer', 'page' => 1])) }}"
               class="px-4 py-2 text-xs font-bold rounded-t-lg transition-all border-b-2 flex items-center gap-1 {{ ($filterBasket ?? '') === 'repeat_customer' ? 'border-green-600 text-green-800 bg-green-50' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
               <span>👤 Pembeli Repeat Order</span>
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <form method="GET" action="{{ route('penjualan.import-shopee') }}" class="mb-4">
            <input type="hidden" name="filter_basket" value="{{ $filterBasket ?? 'all' }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari No. Pesanan, Username, atau Nama Produk..." value="{{ request('search') }}">
                </div>
                <div>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="Pengembalian" {{ request('status') === 'Pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1 justify-center">Cari</button>
                    @if(request('search') || request('status') || request('filter_basket') !== 'all')
                        <a href="{{ route('penjualan.import-shopee') }}" class="btn btn-outline">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        @if($orders->isEmpty())
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="empty-state-title">Tidak Ada Transaksi Ditemukan</div>
                <p>Tidak ditemukan data transaksi yang cocok dengan filter yang Anda pilih.</p>
            </div>
        @else
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 170px;">No. Pesanan</th>
                            <th style="width: 130px;">Waktu Pesanan</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 140px;">Pembeli</th>
                            <th>Produk dalam Keranjang Belanja (Basket)</th>
                            <th style="width: 120px;" class="text-right">Total Bayar</th>
                            <th style="width: 90px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $packingKeywords = ['bubble', 'kardus', 'packing', 'pengaman', 'pelindung paket', 'dus', 'biaya packing'];
                                $pureProducts = [];
                                $packingItems = [];

                                foreach ($order->detail as $d) {
                                    $namaLower = strtolower($d->nama_produk);
                                    $isPacking = false;
                                    foreach ($packingKeywords as $kw) {
                                        if (str_contains($namaLower, $kw)) {
                                            $isPacking = true;
                                            break;
                                        }
                                    }
                                    if ($isPacking) {
                                        $packingItems[] = $d;
                                    } else {
                                        $pureProducts[] = $d;
                                    }
                                }

                                $hasMultiItem = count($pureProducts) >= 2;
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-mono font-bold text-gray-800 text-xs">{{ $order->no_pesanan }}</span>
                                    @if($hasMultiItem)
                                        <div class="mt-1">
                                            <span class="badge badge-info" title="Transaksi ini membeli 2 atau lebih produk murni (FP-Growth Basket)">🛒 Kombinasi</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-xs">{{ \Carbon\Carbon::parse($order->waktu_pesanan_dibuat)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($order->status_pesanan === 'Selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($order->status_pesanan === 'Dibatalkan')
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-warning">{{ $order->status_pesanan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-medium text-xs text-gray-800 block">{{ $order->username_pembeli ?: '-' }}</span>
                                    <span class="text-xs text-gray-400 block">{{ $order->kota ?: '-' }}</span>
                                </td>
                                <td>
                                    <div class="space-y-1">
                                        @foreach($pureProducts as $idx => $prod)
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-3xs flex-shrink-0">{{ $idx + 1 }}</span>
                                                <span class="font-semibold text-gray-800">{{ $prod->nama_produk }}</span>
                                                @if($prod->nama_variasi)
                                                    <span class="text-gray-400 font-normal">({{ $prod->nama_variasi }})</span>
                                                @endif
                                                <span class="badge badge-secondary py-0 px-1 font-bold">x{{ $prod->jumlah }}</span>
                                            </div>
                                        @endforeach

                                        @if(count($packingItems) > 0)
                                            <div class="mt-1 pt-1 border-t border-gray-100 flex items-center gap-1 text-3xs text-gray-400">
                                                <span>+ {{ count($packingItems) }} item kelengkapan:</span>
                                                @foreach($packingItems as $pack)
                                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-500">{{ $pack->nama_produk }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-right text-xs">
                                    <strong class="text-gray-800 block">Rp {{ number_format($order->total_pembayaran) }}</strong>
                                    <span class="text-3xs text-gray-400">{{ count($pureProducts) }} Produk</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline btn-sm p-1" onclick="toggleOrderRow('order-{{ $order->id }}')" title="Detail Nota Transaksi">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr id="order-{{ $order->id }}" style="display: none; background: #fafafa;">
                                <td colspan="7" class="p-4 border-l-4 border-yellow-400">
                                    <div class="mb-2 font-bold text-xs text-gray-700 uppercase">Perincian Nota Transaksi (No. Pesanan: {{ $order->no_pesanan }}):</div>
                                    <div class="bg-white rounded border border-gray-200 overflow-hidden mb-2">
                                        <table class="w-full text-xs">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="p-2">No</th>
                                                    <th class="p-2">SKU Induk</th>
                                                    <th class="p-2">Nama Produk</th>
                                                    <th class="p-2">Variasi</th>
                                                    <th class="p-2 text-center">Qty</th>
                                                    <th class="p-2 text-right">Harga Netto</th>
                                                    <th class="p-2 text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->detail as $idx => $d)
                                                    <tr class="border-b border-gray-100">
                                                        <td class="p-2 text-center">{{ $idx + 1 }}</td>
                                                        <td class="p-2 font-mono text-gray-600">{{ $d->sku_induk ?: '-' }}</td>
                                                        <td class="p-2 font-semibold text-gray-800">{{ $d->nama_produk }}</td>
                                                        <td class="p-2 text-gray-600">{{ $d->nama_variasi ?: '-' }}</td>
                                                        <td class="p-2 text-center font-bold">{{ $d->jumlah }}</td>
                                                        <td class="p-2 text-right">Rp {{ number_format($d->harga_setelah_diskon) }}</td>
                                                        <td class="p-2 text-right font-bold">Rp {{ number_format($d->subtotal_pesanan) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-xs text-gray-500 flex justify-between">
                                        <span>Metode Pembayaran: <strong>{{ $order->metode_pembayaran ?: '-' }}</strong> | Pengiriman: <strong>{{ $order->opsi_pengiriman ?: '-' }}</strong></span>
                                        <span>Alamat: <strong>{{ $order->alamat_pengiriman ?: '-' }}</strong> | Resi: <strong>{{ $order->no_resi ?: '-' }}</strong></span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Hapus Massal -->
    <div id="modalHapusMassal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative border border-gray-100">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="font-bold text-red-600 text-base flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    Hapus Data Transaksi Massal
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 text-xl font-bold" onclick="closeModalHapusMassal()">&times;</button>
            </div>

            <form action="{{ route('penjualan.import-shopee.destroy-massal') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data transaksi Shopee sesuai pilihan ini?')">
                @csrf
                @method('DELETE')

                <div class="space-y-3 mb-5">
                    <label class="block font-semibold text-xs text-gray-700">Pilih Metode Penghapusan:</label>
                    
                    <!-- Opsi 1: Hapus Bulan/Periode -->
                    <div class="border border-gray-200 rounded-xl p-3.5 transition-all hover:border-gray-300 bg-white shadow-xs">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="mode_hapus" value="month" checked onclick="toggleHapusMode('month')" class="mt-0.5 w-4 h-4 text-red-600 focus:ring-red-500 flex-shrink-0">
                            <div class="flex-1">
                                <span class="font-bold text-xs text-gray-800 block">Hapus Transaksi Bulan/Periode Tertentu</span>
                                <span class="text-3xs text-gray-500 block mt-0.5">Menghapus data 1 bulan tertentu untuk persiapan import bulan berikutnya.</span>
                            </div>
                        </label>

                        <div id="containerPilihBulan" class="mt-3 pt-3 border-t border-gray-100">
                            <label for="bulan_tahun" class="block text-xs font-semibold text-gray-700 mb-1">Pilih Bulan &amp; Tahun:</label>
                            <select name="bulan_tahun" id="bulan_tahun" class="form-control text-xs">
                                @foreach($availableMonths as $m)
                                    <option value="{{ $m }}">Bulan Periode {{ \Carbon\Carbon::parse($m.'-01')->format('F Y') }} ({{ $m }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Opsi 2: Reset Total -->
                    <div class="border border-red-200 bg-red-50/60 rounded-xl p-3.5 transition-all hover:bg-red-50">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="mode_hapus" value="all" onclick="toggleHapusMode('all')" class="mt-0.5 text-red-600">
                            <div class="flex-1">
                                <span class="font-bold text-xs text-red-700 block">Reset Total (Hapus SELURUH Transaksi Shopee)</span>
                                <span class="text-3xs text-red-600 block mt-0.5">Menghapus seluruh {{ number_format($totalOrders) }} pesanan transaksi yang tersimpan.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" class="btn btn-outline" onclick="closeModalHapusMassal()">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleOrderRow(id) {
            const el = document.getElementById(id);
            if (el.style.display === 'none') {
                el.style.display = 'table-row';
            } else {
                el.style.display = 'none';
            }
        }

        function openModalHapusMassal() {
            const el = document.getElementById('modalHapusMassal');
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModalHapusMassal() {
            const el = document.getElementById('modalHapusMassal');
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        function toggleHapusMode(mode) {
            const container = document.getElementById('containerPilihBulan');
            if (mode === 'month') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>
