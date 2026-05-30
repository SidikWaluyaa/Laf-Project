<x-app-layout>
    <x-slot name="title">Detail Barang Masuk</x-slot>

    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
        @if(!$barangMasuk->trashed())
        <form method="POST" action="{{ route('barang-masuk.void', $barangMasuk) }}" style="display:inline;" onsubmit="return confirm('VOID transaksi ini? Stok akan dikurangi dan transaksi tidak bisa dibuka kembali.')">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm">✕ Void Transaksi</button>
        </form>
        @else
        <span class="badge badge-danger" style="padding:.5rem 1rem;font-size:.85rem;">VOID</span>
        @endif
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h3>Informasi</h3></div>
        <dl>
            <div class="detail-row"><dt>Bukti Terima</dt><dd><strong>{{ $barangMasuk->nomor_nota ?? '-' }}</strong></dd></div>
            <div class="detail-row"><dt>Tanggal</dt><dd>{{ $barangMasuk->tanggal ? $barangMasuk->tanggal->format('d/m/Y') : '-' }}</dd></div>
            <div class="detail-row"><dt>Supplier</dt><dd>{{ $barangMasuk->supplier->nama_supplier ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Lokasi</dt><dd><span class="badge badge-info">{{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</span></dd></div>
            <div class="detail-row"><dt>Admin</dt><dd>{{ $barangMasuk->admin->name ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Keterangan</dt><dd>{{ $barangMasuk->keterangan ?? '-' }}</dd></div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header"><h3>Detail Item</h3></div>
        <div class="table-container">
            <table>
                <thead><tr><th>Produk</th><th>Qty Masuk</th><th>Harga Beli</th><th>Subtotal</th></tr></thead>
                <tbody>
                    @foreach($barangMasuk->detail as $d)
                    <tr>
                        <td>{{ $d->produk->kode_barang ?? '' }} - {{ $d->produk->nama_barang ?? '' }}</td>
                        <td>{{ number_format($d->qty_masuk) }}</td>
                        <td>Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($d->qty_masuk * $d->harga_beli, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background:#fafafa;font-weight:700;">
                        <td colspan="3">TOTAL</td>
                        <td>Rp {{ number_format($barangMasuk->detail->sum(fn($d) => $d->qty_masuk * $d->harga_beli), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
