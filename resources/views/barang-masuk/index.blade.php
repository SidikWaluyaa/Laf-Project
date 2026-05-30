<x-app-layout>
    <x-slot name="title">Barang Masuk</x-slot>

    <div class="page-header">
        <form method="GET" class="search-bar" style="margin-bottom:0;">
            <input type="text" name="search" class="form-control" placeholder="Cari supplier..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm" type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </form>
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">+ Barang Masuk</a>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Tanggal</th><th>Nomor Bukti</th><th>Supplier</th><th>Lokasi</th><th>Admin</th><th>Items</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($barangMasuk as $bm)
                    <tr>
                        <td>{{ $bm->tanggal ? $bm->tanggal->format('d/m/Y') : '-' }}</td>
                        <td><strong>{{ $bm->nomor_nota ?? '-' }}</strong></td>
                        <td>{{ $bm->supplier->nama_supplier ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $bm->lokasi->nama_lokasi ?? '-' }}</span></td>
                        <td>{{ $bm->admin->name ?? '-' }}</td>
                        <td>{{ $bm->detail->count() }} item</td>
                        <td><a href="{{ route('barang-masuk.show', $bm) }}" class="btn btn-outline btn-sm">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                <p class="empty-state-title">Belum ada data barang masuk</p>
                                <p>Catat penerimaan barang pertama Anda</p>
                                <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary btn-sm">+ Barang Masuk</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $barangMasuk->links() }}</div>
    </div>
</x-app-layout>
