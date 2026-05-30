<x-app-layout>
    <x-slot name="title">Stok Minimum</x-slot>

    <div class="page-header">
        <h3 style="margin:0;font-weight:700;font-size:1rem;">Monitoring Stok Minimum</h3>
        <a href="{{ route('stok-minimum.create') }}" class="btn btn-primary btn-sm">+ Atur Stok Minimum</a>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Total Stok</th><th>Stok Minimum</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td><strong>{{ $p->kode_barang }}</strong></td>
                        <td>{{ $p->nama_barang }}</td>
                        <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ number_format($p->total_stok_all) }}</td>
                        <td>
                            <form method="POST" action="{{ route('stok-minimum.update', $p->id) }}" style="display:flex;gap:.25rem;align-items:center;">
                                @csrf
                                @method('PUT')
                                <input type="number" name="stok_minimum" value="{{ $p->stokMinimum->stok_minimum ?? 0 }}" min="0" class="form-control" style="width:80px;padding:.25rem .5rem;font-size:.85rem;">
                                <button type="submit" class="btn btn-outline btn-sm" style="padding:.25rem .5rem;" title="Simpan">✓</button>
                            </form>
                        </td>
                        <td>
                            @if($p->is_low)
                                <span class="badge badge-danger">LOW STOCK</span>
                            @else
                                <span class="badge badge-success">AMAN</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('stok-minimum.destroy', $p->id) }}" style="display:inline;" onsubmit="return confirm('Hapus stok minimum untuk produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:.25rem .5rem;font-size:.75rem;">✕</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                <p class="empty-state-title">Belum ada data stok minimum</p>
                                <p>Atur batas minimum stok untuk monitoring otomatis</p>
                                <a href="{{ route('stok-minimum.create') }}" class="btn btn-primary btn-sm">+ Atur Stok Minimum</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
