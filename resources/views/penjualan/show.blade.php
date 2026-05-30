<x-app-layout>
    <x-slot name="title">Detail Penjualan</x-slot>

    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
        @if(!$penjualan->trashed())
        <form method="POST" action="{{ route('penjualan.void', $penjualan) }}" style="display:inline;" onsubmit="return confirm('VOID transaksi ini? Stok akan dikembalikan dan transaksi tidak bisa dibuka kembali.')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm">✕ Void Transaksi</button>
        </form>
        @else
        <span class="badge badge-danger" style="padding:.5rem 1rem;font-size:.85rem;">VOID</span>
        @endif
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h3>Informasi Penjualan</h3></div>
        <dl>
            <div class="detail-row"><dt>Tanggal</dt><dd>{{ $penjualan->tanggal->format('d/m/Y') }}</dd></div>
            <div class="detail-row"><dt>Nomor Nota</dt><dd>{{ $penjualan->nomor_nota ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Tipe Nota</dt><dd>{{ $penjualan->tipe_nota ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Pelanggan</dt><dd>{{ $penjualan->pelanggan->nama_pelanggan ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Lokasi</dt><dd><span class="badge badge-info">{{ $penjualan->lokasi->nama_lokasi ?? '-' }}</span></dd></div>
            <div class="detail-row"><dt>Admin</dt><dd>{{ $penjualan->admin->name ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Keterangan</dt><dd>{{ $penjualan->keterangan ?? '-' }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header"><h3>Detail Item</h3></div>
        <div class="table-container">
            <table>
                <thead><tr><th>Produk</th><th>Qty Keluar</th><th>HPP Snapshot</th><th>Subtotal</th></tr></thead>
                <tbody>
                    @foreach($penjualan->detail as $d)
                    <tr>
                        <td>{{ $d->produk->kode_barang ?? '' }} - {{ $d->produk->nama_barang ?? '' }}</td>
                        <td>{{ number_format($d->qty_keluar) }}</td>
                        <td>Rp {{ number_format($d->hpp_snapshot, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($d->qty_keluar * $d->hpp_snapshot, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background:#fafafa;font-weight:700;">
                        <td colspan="3">TOTAL</td>
                        <td>Rp {{ number_format($penjualan->detail->sum(fn($d) => $d->qty_keluar * $d->hpp_snapshot), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
