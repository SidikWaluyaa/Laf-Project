<x-app-layout>
    <x-slot name="title">Master Satuan</x-slot>

    {{-- Success / Error Message --}}
    @if(session('success'))
    <div style="background:#d4edda;color:#155724;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;border:1px solid #c3e6cb;">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#f8d7da;color:#721c24;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-weight:600;border:1px solid #f5c6cb;">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:380px 1fr;gap:1.25rem;">
        {{-- LEFT: Form Tambah --}}
        <div class="card" style="align-self:start;">
            <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:700;">➕ Tambah Satuan Baru</h3>
            <form method="POST" action="{{ route('satuan.store') }}">
                @csrf
                <div class="form-group">
                    <label for="nama_satuan">Nama Satuan</label>
                    <input type="text" id="nama_satuan" name="nama_satuan" class="form-control" placeholder="Contoh: PCS, SET, BOX" value="{{ old('nama_satuan') }}" required style="text-transform:uppercase;">
                    @error('nama_satuan') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan (opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Pieces / Satuan" value="{{ old('keterangan') }}">
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%;">Simpan</button>
            </form>
        </div>

        {{-- RIGHT: Table --}}
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3 style="margin:0;font-size:1rem;font-weight:700;">📋 Daftar Satuan</h3>
                <form method="GET" style="display:flex;gap:.5rem;">
                    <input type="text" name="search" class="form-control" placeholder="Cari satuan..." value="{{ request('search') }}" style="width:200px;">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th>Nama Satuan</th>
                            <th>Keterangan</th>
                            <th style="text-align:center;">Jumlah Produk</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($satuanList as $i => $s)
                        <tr id="row-{{ $s->id }}">
                            <td>{{ $satuanList->firstItem() + $i }}</td>
                            <td>
                                <form method="POST" action="{{ route('satuan.update', $s) }}" style="display:inline;">
                                    @csrf @method('PUT')
                                    <input type="text" name="nama_satuan" value="{{ $s->nama_satuan }}" class="form-control" style="width:120px;display:inline-block;text-transform:uppercase;font-weight:700;" required>
                            </td>
                            <td>
                                    <input type="text" name="keterangan" value="{{ $s->keterangan }}" class="form-control" style="width:180px;display:inline-block;font-size:.85rem;">
                            </td>
                            <td style="text-align:center;">
                                <span class="badge {{ $s->produk_count > 0 ? 'badge-info' : 'badge-secondary' }}">{{ $s->produk_count }} produk</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:.3rem;justify-content:center;">
                                    <button type="submit" class="btn btn-sm" style="background:#f59e0b;color:#fff;padding:.3rem .6rem;font-size:.75rem;">Simpan</button>
                                </form>
                                @if($s->produk_count === 0)
                                    <form method="POST" action="{{ route('satuan.destroy', $s) }}" style="display:inline;" onsubmit="return confirm('Hapus satuan {{ $s->nama_satuan }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:#ef4444;color:#fff;padding:.3rem .6rem;font-size:.75rem;">Hapus</button>
                                    </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state"><p>Belum ada data satuan.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:.75rem;">{{ $satuanList->links() }}</div>
        </div>
    </div>
</x-app-layout>
