<x-app-layout>
    <x-slot name="title">Penjualan</x-slot>

    <div class="page-header">
        <form method="GET" class="search-bar" style="margin-bottom:0;">
            <input type="text" name="search" class="form-control" placeholder="Cari nota / pelanggan..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm" type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
        </form>
        <a href="{{ route('penjualan.create') }}" class="btn btn-primary">+ Penjualan Baru</a>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Tanggal</th><th>No. Nota</th><th>Pelanggan</th><th>Lokasi</th><th>Admin</th><th>Items</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($penjualan as $p)
                    <tr>
                        <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td><strong>{{ $p->nomor_nota ?? '-' }}</strong></td>
                        <td>{{ $p->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $p->lokasi->nama_lokasi ?? '-' }}</span></td>
                        <td>{{ $p->admin->name ?? '-' }}</td>
                        <td>{{ $p->detail->count() }} item</td>
                        <td><a href="{{ route('penjualan.show', $p) }}" class="btn btn-outline btn-sm">Detail</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <p class="empty-state-title">Belum ada data penjualan</p>
                                <p>Buat transaksi penjualan pertama</p>
                                <a href="{{ route('penjualan.create') }}" class="btn btn-primary btn-sm">+ Penjualan Baru</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $penjualan->links() }}</div>
    </div>
</x-app-layout>
