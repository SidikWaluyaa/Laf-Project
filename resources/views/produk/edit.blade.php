<x-app-layout>
    <x-slot name="title">Edit Produk</x-slot>

    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('produk.update', $produk) }}">
            @csrf @method('PUT')

            <!-- Kode Barang (read-only) -->
            <div class="form-group">
                <label for="kodePreview">Kode Barang</label>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <input type="text" id="kodePreview" class="form-control" value="{{ $produk->kode_barang }}" readonly
                           style="background:#f8f9fa;font-weight:700;color:var(--accent-green);letter-spacing:1px;max-width:250px;">
                    <span id="kodeNotice" style="font-size:.78rem;color:#666;display:none;">⚠️ Kode akan berubah karena kategori beda</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategoriSelect">Kategori</label>
                    <select name="kategori_id" id="kategoriSelect" class="form-control tom-select" data-placeholder="Ketik nama kategori..." required>
                        @foreach($kategori as $k)
                        <option value="{{ $k->id }}" data-prefix="{{ $k->kode_prefix }}" {{ old('kategori_id', $produk->kategori_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }} ({{ $k->kode_prefix }})
                        </option>
                        @endforeach
                    </select>
                    @error('kategori_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control" value="{{ old('nama_barang', $produk->nama_barang) }}" required>
                    @error('nama_barang') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="variasi_barang">Variasi Barang</label>
                    <input type="text" id="variasi_barang" name="variasi_barang" class="form-control" value="{{ old('variasi_barang', $produk->variasi_barang) }}">
                </div>
                <div class="form-group">
                    <label for="satuan_id">Satuan</label>
                    <div style="display:flex;gap:.5rem;align-items:center;">
                        <select id="satuan_id" name="satuan_id" class="form-control tom-select" data-placeholder="Pilih Satuan..." required style="flex:1;">
                            @foreach($satuanList as $s)
                            <option value="{{ $s->id }}" {{ old('satuan_id', $produk->satuan_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_satuan }}
                            </option>
                            @endforeach
                        </select>
                        <a href="{{ route('satuan.index') }}" class="btn btn-outline btn-sm" title="Kelola Satuan">⚙️</a>
                    </div>
                    @error('satuan_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group" style="max-width:300px;">
                <label for="hpp">HPP (Rp)</label>
                <input type="number" id="hpp" name="hpp" class="form-control" value="{{ old('hpp', $produk->hpp) }}" min="0" step="100" required>
            </div>
            <div style="display:flex;gap:.5rem;margin-top:1rem;">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('produk.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const originalKategori = '{{ $produk->kategori_id }}';
            document.getElementById('kategoriSelect').addEventListener('change', function() {
                const preview = document.getElementById('kodePreview');
                const notice = document.getElementById('kodeNotice');
                if (this.value && this.value !== originalKategori) {
                    fetch(`/api/produk/next-code/${this.value}`)
                        .then(r => r.json())
                        .then(data => {
                            preview.value = data.kode;
                            notice.style.display = 'inline';
                        });
                } else {
                    preview.value = '{{ $produk->kode_barang }}';
                    notice.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
