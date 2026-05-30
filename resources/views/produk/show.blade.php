<x-app-layout>
    <x-slot name="title">Detail Produk</x-slot>

    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
        <a href="{{ route('produk.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
        <a href="{{ route('produk.edit', $produk) }}" class="btn btn-warning btn-sm">Edit</a>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h3>Informasi Produk</h3></div>
        <dl>
            <div class="detail-row"><dt>Kode Barang</dt><dd><strong>{{ $produk->kode_barang }}</strong></dd></div>
            <div class="detail-row"><dt>Nama Barang</dt><dd>{{ $produk->nama_barang }}</dd></div>
            <div class="detail-row"><dt>Variasi</dt><dd>{{ $produk->variasi_barang ?? '-' }}</dd></div>
            <div class="detail-row"><dt>Kategori</dt><dd><span class="badge badge-info">{{ $produk->kategori->nama_kategori ?? '-' }}</span></dd></div>
            <div class="detail-row"><dt>Satuan</dt><dd>{{ $produk->satuan }}</dd></div>
            <div class="detail-row"><dt>HPP</dt><dd>Rp {{ number_format($produk->hpp, 0, ',', '.') }}</dd></div>
        </dl>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h3>Stok per Lokasi</h3></div>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Lokasi</th><th>Total Stok</th><th>Nilai (HPP × Stok)</th></tr>
                </thead>
                <tbody>
                    @foreach($produk->stokProduk as $sp)
                    <tr>
                        <td>{{ $sp->lokasi->nama_lokasi ?? '-' }}</td>
                        <td><strong>{{ number_format($sp->total_stok) }}</strong></td>
                        <td>Rp {{ number_format($produk->hpp * $sp->total_stok, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#fafafa;font-weight:700;">
                        <td>TOTAL</td>
                        <td>{{ number_format($produk->stokProduk->sum('total_stok')) }}</td>
                        <td>Rp {{ number_format($produk->hpp * $produk->stokProduk->sum('total_stok'), 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- #8: Riwayat Mutasi Stok --}}
    <div class="card">
        <div class="card-header"><h3>Riwayat Mutasi Stok (50 Terakhir)</h3></div>
        <div class="table-container" style="max-height:400px;overflow-y:auto;">
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Tipe</th><th>Qty</th><th>Harga Beli</th><th>HPP Snapshot</th><th>Keterangan</th></tr>
                </thead>
                <tbody>
                    @forelse($mutasi as $m)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
                        <td>
                            @if($m->tipe === 'MASUK')
                                <span class="badge badge-success">MASUK</span>
                            @else
                                <span class="badge badge-danger">KELUAR</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($m->qty) }}</strong></td>
                        <td>{{ $m->harga ? 'Rp ' . number_format($m->harga, 0, ',', '.') : '-' }}</td>
                        <td>{{ $m->hpp_snapshot ? 'Rp ' . number_format($m->hpp_snapshot, 0, ',', '.') : '-' }}</td>
                        <td>{{ Str::limit($m->keterangan, 30) ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state" style="padding:1.5rem;"><p>Belum ada mutasi stok</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
