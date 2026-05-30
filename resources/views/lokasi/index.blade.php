<x-app-layout>
    <x-slot name="title">Lokasi</x-slot>

    <div class="card" style="max-width:600px;margin-bottom:1.5rem;">
        <div class="card-header"><h3>Tambah Lokasi</h3></div>
        <form method="POST" action="{{ route('lokasi.store') }}" style="display:flex;gap:.5rem;">
            @csrf
            <input type="text" name="nama_lokasi" class="form-control" placeholder="Nama lokasi (TOKO, GUDANG...)" required>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Nama Lokasi</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($lokasi as $l)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('lokasi.update', $l) }}" style="display:flex;gap:.5rem;">
                                @csrf @method('PUT')
                                <input type="text" name="nama_lokasi" class="form-control" value="{{ $l->nama_lokasi }}" style="max-width:300px;">
                                <button class="btn btn-warning btn-sm">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('lokasi.destroy', $l) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2"><div class="empty-state"><p>Belum ada data lokasi.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $lokasi->links() }}</div>
    </div>
</x-app-layout>
