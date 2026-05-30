<x-app-layout>
    <x-slot name="title">Stok per Gudang</x-slot>

    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3>Pilih Lokasi Gudang</h3>
        </div>
        <form method="GET" action="{{ route('stok-lokasi.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                <label for="lokasi_id">Lokasi</label>
                <select name="lokasi_id" id="lokasi_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($lokasiList as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ $selectedLokasiId == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if($selectedLokasiId)
            <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 250px;">
                <label for="search">Cari Produk</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('stok-lokasi.index', ['lokasi_id' => $selectedLokasiId]) }}" class="btn btn-outline" title="Reset Pencarian">✕</a>
                    @endif
                </div>
            </div>
            @endif
        </form>
    </div>

    @if($selectedLokasiId && $produk)
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Data Stok di Lokasi: <strong>{{ $lokasiList->firstWhere('id', $selectedLokasiId)?->nama_lokasi }}</strong></h3>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align: right;">Stok Fisik Tersedia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk as $p)
                        @php
                            $stokFisik = $p->stokProduk->first()?->total_stok ?? 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $p->kode_barang }}</strong></td>
                            <td>
                                {{ $p->nama_barang }}
                                @if($p->variasi_barang)
                                    <span style="color: var(--text-secondary); font-size: 0.85em;">({{ $p->variasi_barang }})</span>
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $p->kategori->nama_kategori ?? '-' }}</span></td>
                            <td style="text-align: right;">
                                <span class="badge {{ $stokFisik > 0 ? 'badge-success' : 'badge-danger' }}" style="font-size: 100%;">
                                    {{ $stokFisik }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="empty-state-title">Tidak ada produk ditemukan</p>
                                    <p>Gunakan kata kunci pencarian yang berbeda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $produk->links() }}
        </div>
    </div>
    @elseif(!$selectedLokasiId)
    <div style="text-align: center; padding: 4rem 2rem; color: var(--text-secondary); background: var(--bg-secondary); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.5;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <p style="font-size: 1.1rem; margin-bottom: 0;">Pilih lokasi gudang terlebih dahulu pada form di atas untuk melihat rincian stok.</p>
    </div>
    @endif
</x-app-layout>
