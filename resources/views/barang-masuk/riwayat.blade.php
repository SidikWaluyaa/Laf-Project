<x-app-layout>
    <x-slot name="title">Riwayat Barang Masuk</x-slot>

    {{-- Filter Bar --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('barang-masuk.riwayat') }}">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;flex:2;min-width:200px;">
                    <label>🔍 Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik kode/nama produk..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px;">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control tom-select" data-placeholder="Semua Supplier">
                        <option value="">Semua</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:130px;">
                    <label>Lokasi</label>
                    <select name="lokasi_id" class="form-control tom-select" data-placeholder="Semua Lokasi">
                        <option value="">Semua</option>
                        @foreach($lokasiList as $l)
                        <option value="{{ $l->id }}" {{ request('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:130px;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:130px;">
                    <label>Sampai</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('barang-masuk.riwayat') }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('barang-masuk.riwayat.pdf', request()->all()) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="stats-grid" style="grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));margin-bottom:1.5rem;">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <p class="stat-value">{{ number_format($totalQty) }}</p>
            <p class="stat-label">Total Qty Masuk</p>
        </div>
        <div class="stat-card stat-yellow">
            <div class="stat-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="stat-value">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
            <p class="stat-label">Total Nilai Pembelian</p>
        </div>
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="stat-value">{{ $riwayat->total() }}</p>
            <p class="stat-label">Total Entri</p>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Supplier / Pengirim</th>
                        <th>Keterangan</th>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Variasi</th>
                        <th style="text-align:center;">QTY Masuk</th>
                        <th>Satuan</th>
                        <th style="text-align:right;">Harga Beli</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th>Diinput Oleh</th>
                        <th>Lokasi Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->barangMasuk->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $r->barangMasuk->supplier->nama_supplier ?? '-' }}</td>
                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->barangMasuk->keterangan }}">
                            {{ $r->barangMasuk->keterangan ?? '-' }}
                        </td>
                        <td><strong>{{ $r->produk->kode_barang }}</strong></td>
                        <td>{{ $r->produk->nama_barang }}</td>
                        <td>{{ $r->produk->variasi_barang ?? '-' }}</td>
                        <td style="text-align:center;">
                            <span style="background:#d4edda;color:#155724;padding:.2rem .6rem;border-radius:6px;font-weight:700;">
                                {{ $r->qty_masuk }}
                            </span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td style="text-align:right;white-space:nowrap;">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</td>
                        <td style="text-align:right;white-space:nowrap;font-weight:600;">Rp {{ number_format($r->qty_masuk * $r->harga_beli, 0, ',', '.') }}</td>
                        <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align:center;padding:2rem;color:#666;">
                            @if(request()->hasAny(['search','supplier_id','lokasi_id','dari','sampai']))
                                Tidak ada data yang cocok dengan filter.
                            @else
                                Belum ada riwayat barang masuk.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayat->links() }}</div>
    </div>
</x-app-layout>
