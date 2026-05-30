<x-app-layout>
    <x-slot name="title">Pelanggan</x-slot>

    <div class="card" style="max-width:600px;margin-bottom:1.5rem;">
        <div class="card-header"><h3>Tambah Pelanggan</h3></div>
        <form method="POST" action="{{ route('pelanggan.store') }}" style="display:flex;gap:.5rem;">
            @csrf
            <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama pelanggan..." required>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Nama Pelanggan</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($pelanggan as $pl)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('pelanggan.update', $pl) }}" style="display:flex;gap:.5rem;">
                                @csrf @method('PUT')
                                <input type="text" name="nama_pelanggan" class="form-control" value="{{ $pl->nama_pelanggan }}" style="max-width:300px;">
                                <button class="btn btn-warning btn-sm">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('pelanggan.destroy', $pl) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2"><div class="empty-state"><p>Belum ada data pelanggan.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $pelanggan->links() }}</div>
    </div>
</x-app-layout>
