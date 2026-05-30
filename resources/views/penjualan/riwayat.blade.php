<x-app-layout>
    <x-slot name="title">Riwayat Penjualan</x-slot>

    <div class="card" style="margin-bottom:1.5rem;">
        <form method="GET" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;gap:.75rem;align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Cari Produk</label>
                <input type="text" name="search" class="form-control" placeholder="Kode / Nama produk..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Pelanggan</label>
                <select name="pelanggan_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($pelangganList as $p)
                    <option value="{{ $p->id }}" {{ request('pelanggan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Lokasi</label>
                <select name="lokasi_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($lokasiList as $l)
                    <option value="{{ $l->id }}" {{ request('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div style="display:flex;gap:.5rem;">
                <button class="btn btn-primary btn-sm" type="submit">Filter</button>
                <a href="{{ route('penjualan.riwayat.pdf', request()->all()) }}" class="btn btn-outline btn-sm" target="_blank">📄 PDF</a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-bottom:1rem;">
        <div style="display:flex;gap:2rem;">
            <div><strong>Total Qty Keluar:</strong> {{ number_format($totalQty) }}</div>
            <div><strong>Total Nilai:</strong> Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Nota</th><th>Pelanggan</th><th>Kode</th><th>Produk</th><th>Qty</th><th>HPP</th><th>Subtotal</th><th>Lokasi</th></tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td>{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
                        <td>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td><strong>{{ $r->produk->kode_barang }}</strong></td>
                        <td>{{ $r->produk->nama_barang }}</td>
                        <td>{{ number_format($r->qty_keluar) }}</td>
                        <td>Rp {{ number_format($r->hpp_snapshot, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($r->qty_keluar * $r->hpp_snapshot, 0, ',', '.') }}</strong></td>
                        <td><span class="badge badge-info">{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <p>Belum ada data riwayat penjualan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayat->links() }}</div>
    </div>
</x-app-layout>
