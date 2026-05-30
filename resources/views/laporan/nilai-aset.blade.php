<x-app-layout>
    <x-slot name="title">Laporan Nilai Aset</x-slot>

    <div class="card" style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <h3 style="margin:0;">Laporan Nilai Aset Barang</h3>
            <div style="display:flex;gap:.5rem;align-items:center;">
                <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
                    <select name="kategori_id" class="form-control" style="min-width:180px;padding:.35rem .5rem;font-size:.85rem;" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ $kategoriId == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('laporan.nilai-aset.pdf', ['kategori_id' => $kategoriId]) }}" class="btn btn-primary btn-sm" target="_blank">📄 Export PDF</a>
            </div>
        </div>
        <div class="stat-card stat-dark" style="margin:1rem 0 0;padding:.75rem 1.5rem;">
            <p class="stat-label" style="margin:0;font-size:.72rem;">Total Nilai Aset</p>
            <p class="stat-value" style="font-size:1.25rem;margin:0;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Kode</th><th>Nama Barang</th><th>Variasi</th><th>Kategori</th><th>HPP</th><th>Total Stok</th><th>Nilai</th></tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><strong>{{ $item->kode_barang }}</strong></td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->variasi_barang ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $item->kategori }}</span></td>
                        <td>Rp {{ number_format($item->hpp, 0, ',', '.') }}</td>
                        <td>{{ number_format($item->total_stok) }}</td>
                        <td><strong>Rp {{ number_format($item->nilai, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background:#fafafa;font-weight:700;font-size:1rem;">
                        <td colspan="6">TOTAL NILAI ASET</td>
                        <td>Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
