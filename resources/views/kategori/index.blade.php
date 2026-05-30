<x-app-layout>
    <x-slot name="title">Kategori</x-slot>

    <div class="card" style="max-width:700px;margin-bottom:1.5rem;">
        <div class="card-header"><h3>Tambah Kategori</h3></div>
        <form method="POST" action="{{ route('kategori.store') }}" style="display:flex;gap:.5rem;align-items:end;">
            @csrf
            <div class="form-group" style="margin-bottom:0;flex:1;">
                <label for="nama_kategori">Nama Kategori</label>
                <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" placeholder="Contoh: Sandal Gunung" required>
            </div>
            <div class="form-group" style="margin-bottom:0;width:120px;">
                <label for="kode_prefix">Kode Prefix</label>
                <input type="text" id="kode_prefix" name="kode_prefix" class="form-control" placeholder="SG" maxlength="5" style="text-transform:uppercase;font-weight:700;letter-spacing:1px;" required>
            </div>
            <button class="btn btn-primary" style="height:38px;">Tambah</button>
        </form>
        @error('kode_prefix') <div class="form-error" style="margin-top:.5rem;">{{ $message }}</div> @enderror
    </div>

    <div class="card">
        <div class="card-header"><h3>Daftar Kategori</h3></div>
        <div class="table-container">
            <table>
                <thead><tr><th>Nama Kategori</th><th>Kode Prefix</th><th>Format Kode</th><th>Produk</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($kategori as $k)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('kategori.update', $k) }}" style="display:flex;gap:.5rem;" id="formKategori{{ $k->id }}">
                                @csrf @method('PUT')
                                <input type="text" name="nama_kategori" class="form-control" value="{{ $k->nama_kategori }}" style="max-width:220px;">
                                <input type="text" name="kode_prefix" class="form-control" value="{{ $k->kode_prefix }}" style="max-width:80px;text-transform:uppercase;font-weight:700;letter-spacing:1px;" maxlength="5">
                                <button class="btn btn-warning btn-sm">Update</button>
                            </form>
                        </td>
                        <td><span class="badge badge-info" style="font-weight:800;letter-spacing:1px;">{{ $k->kode_prefix }}</span></td>
                        <td style="font-family:monospace;font-size:.8rem;color:#666;">LAF-{{ $k->kode_prefix }}-001</td>
                        <td>{{ $k->produk_count ?? 0 }}</td>
                        <td>
                            <form method="POST" action="{{ route('kategori.destroy', $k) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><p>Belum ada data kategori.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $kategori->links() }}</div>
    </div>
</x-app-layout>
