<x-app-layout>
    <x-slot name="title">Atur Stok Minimum</x-slot>

    <div class="card" style="max-width:500px;">
        <form method="POST" action="{{ route('stok-minimum.store') }}">
            @csrf
            <div class="form-group">
                <label for="produk_id">Produk</label>
                <select id="produk_id" name="produk_id" class="form-control tom-select" data-placeholder="Ketik kode/nama produk..." required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produk as $p)
                    <option value="{{ $p->id }}">{{ $p->kode_barang }} - {{ $p->nama_barang }} ({{ $p->variasi_barang }})</option>
                    @endforeach
                </select>
                @error('produk_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="stok_minimum">Stok Minimum</label>
                <input type="number" id="stok_minimum" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', 10) }}" min="0" required>
                @error('stok_minimum') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('stok-minimum.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
