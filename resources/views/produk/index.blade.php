<x-app-layout>
    <x-slot name="title">Master Produk</x-slot>

    <div class="page-header">
        <form method="GET" class="search-bar" style="margin-bottom:0;">
            <input type="text" name="search" class="form-control" placeholder="Cari kode / nama barang..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm" type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </form>
        <div class="page-header-actions">
            <a href="{{ route('produk.template') }}" class="btn btn-outline btn-sm" title="Download Template Excel">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Template
            </a>
            <button x-data="" type="button" class="btn btn-secondary btn-sm" x-on:click.prevent="$dispatch('open-modal', 'import-modal')" title="Import dari Excel">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import
            </button>
            <a href="{{ route('produk.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        </div>
    </div>

    <!-- Import Modal -->
    <x-modal name="import-modal" focusable maxWidth="lg">
        <div style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">📥 Import Produk dari Excel</h3>
                <button x-on:click="$dispatch('close')" style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-secondary);" aria-label="Tutup modal">✕</button>
            </div>
            <form method="POST" action="{{ route('produk.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file_import">Pilih File Excel (.xlsx)</label>
                    <input type="file" id="file_import" name="file" class="form-control" accept=".xlsx,.xls" required>
                </div>
                <div style="background:#FFF8E1;border-radius:var(--radius-md);padding:1rem;margin-bottom:1.5rem;font-size:.82rem;border:1px solid #FFECB3;">
                    <strong>💡 Panduan:</strong>
                    <ol style="margin:.5rem 0 0;padding-left:1.25rem;">
                        <li>Download template dulu → klik tombol <strong>"Template"</strong></li>
                        <li>Isi data di sheet <strong>"Template"</strong>, hapus baris contoh (kuning)</li>
                        <li>Gunakan dropdown untuk kolom <strong>Kategori</strong> & <strong>Satuan</strong></li>
                        <li>Kode Barang akan <strong>otomatis digenerate</strong> oleh sistem</li>
                        <li>Upload file yang sudah diisi</li>
                    </ol>
                </div>
                <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                    <button type="button" class="btn btn-outline" x-on:click="$dispatch('close')">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Stock Detail Modal -->
    <x-modal name="stock-modal" focusable maxWidth="sm">
        <div style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <div>
                    <h3 style="margin:0;font-size:1rem;font-weight:700;">📦 Rincian Stok Gudang</h3>
                    <p id="stock-modal-product-name" style="margin:0;font-size:0.85rem;color:var(--text-secondary);"></p>
                </div>
                <button x-on:click="$dispatch('close')" style="background:none;border:none;font-size:1.25rem;cursor:pointer;color:var(--text-secondary);" aria-label="Tutup modal">✕</button>
            </div>
            <div class="table-container" style="border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <table style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="padding:.5rem 1rem;">Lokasi / Gudang</th>
                            <th style="padding:.5rem 1rem; text-align:right;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="stock-modal-body">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" class="btn btn-outline btn-sm" x-on:click="$dispatch('close')">Tutup</button>
            </div>
        </div>
    </x-modal>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Variasi</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>HPP</th>
                        <th>Total Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk as $p)
                    <tr>
                        <td><strong>{{ $p->kode_barang }}</strong></td>
                        <td>{{ $p->nama_barang }}</td>
                        <td>{{ $p->variasi_barang ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $p->kategori->nama_kategori ?? '-' }}</span></td>
                        <td>{{ $p->satuan }}</td>
                        <td>Rp {{ number_format($p->hpp, 0, ',', '.') }}</td>
                        @php
                            $totalStok = $p->stokProduk->sum('total_stok');
                            $lokasiData = $p->stokProduk->map(function($sp) {
                                return [
                                    'nama' => $sp->lokasi->nama_lokasi ?? 'Unknown',
                                    'qty' => $sp->total_stok
                                ];
                            });
                        @endphp
                        <td>
                            <button x-data="" 
                                    x-on:click="$dispatch('open-modal', 'stock-modal'); showStockDetail('{{ $p->nama_barang }}', {{ json_encode($lokasiData) }})" 
                                    class="badge {{ $totalStok > 0 ? 'badge-success' : 'badge-danger' }}" 
                                    style="border:none; cursor:pointer; font-size:100%;">
                                {{ $totalStok }}
                            </button>
                        </td>
                        <td>
                            <div style="display:flex;gap:.35rem;">
                                <a href="{{ route('produk.show', $p) }}" class="btn btn-outline btn-sm">Detail</a>
                                <a href="{{ route('produk.edit', $p) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="{{ route('produk.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="empty-state-title">Belum ada data produk</p>
                                <p>Mulai dengan menambahkan produk pertama Anda</p>
                                <a href="{{ route('produk.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function showStockDetail(productName, stockData) {
            document.getElementById('stock-modal-product-name').innerText = productName;
            const tbody = document.getElementById('stock-modal-body');
            tbody.innerHTML = '';

            let total = 0;
            if (stockData && stockData.length > 0) {
                stockData.forEach(item => {
                    total += parseInt(item.qty);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="padding:.5rem 1rem;">${item.nama}</td>
                        <td style="padding:.5rem 1rem; text-align:right; font-weight:600;">${item.qty}</td>
                    `;
                    tbody.appendChild(tr);
                });
                
                // Add total row
                const trTotal = document.createElement('tr');
                trTotal.style.backgroundColor = 'var(--bg-secondary)';
                trTotal.innerHTML = `
                    <td style="padding:.5rem 1rem; font-weight:700;">TOTAL STOK</td>
                    <td style="padding:.5rem 1rem; text-align:right; font-weight:700; color:var(--primary);">${total}</td>
                `;
                tbody.appendChild(trTotal);
            } else {
                tbody.innerHTML = `<tr><td colspan="2" style="text-align:center; padding:1rem; color:var(--text-secondary);">Tidak ada data stok di gudang manapun.</td></tr>`;
            }
        }
    </script>
    @endpush
</x-app-layout>
