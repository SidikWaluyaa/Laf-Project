<x-app-layout>
    <x-slot name="title">Input Barang Masuk</x-slot>

    <div class="card">
        <form method="POST" action="{{ route('barang-masuk.store') }}" id="formBarangMasuk">
            @csrf
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="nomor_nota">Nomor Bukti (Otomatis)</label>
                    <input type="text" id="nomor_nota" name="nomor_nota" class="form-control" value="{{ $autoNomorNota }}" readonly style="background-color: var(--bg-color); font-weight: 600;">
                </div>
                <div class="form-group" style="flex: 1.5;">
                    <label for="supplier_id">Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-control tom-select" data-placeholder="Cari supplier..." required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1.5;">
                    <label for="lokasi_id">Lokasi</label>
                    <select id="lokasi_id" name="lokasi_id" class="form-control tom-select" data-placeholder="Cari lokasi..." required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasi as $l)
                        <option value="{{ $l->id }}" {{ old('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="tanggal">Tanggal Penerimaan</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group" style="flex: 3;">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Opsional (Contoh: Nama Supir / Plat Nomor)">
                </div>
            </div>

            <h4 style="margin:1.5rem 0 1rem;font-weight:700;">Detail Item</h4>
            <div id="detail-rows">
                <div class="detail-input-row" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Produk</label>
                        <select name="details[0][produk_id]" class="form-control produk-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produk as $p)
                            <option value="{{ $p->id }}">{{ $p->kode_barang }} - {{ $p->nama_barang }} ({{ $p->variasi_barang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Qty Masuk</label>
                        <input type="number" name="details[0][qty_masuk]" class="form-control" min="1" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Harga Beli (Rp)</label>
                        <input type="number" name="details[0][harga_beli]" class="form-control" min="0" step="100" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;" aria-label="Hapus baris">✕</button>
                </div>
            </div>

            <button type="button" class="btn btn-outline btn-sm" onclick="addDetailRow()" style="margin-bottom:1.5rem;">+ Tambah Item</button>

            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Build produk options once for reuse
            const produkOptions = @json($produk->map(fn($p) => ['id' => $p->id, 'text' => $p->kode_barang.' - '.$p->nama_barang.' ('.$p->variasi_barang.')']));

            // Init Tom Select for first row produk select
            document.querySelectorAll('.produk-select').forEach(initProdukSelect);

            function initProdukSelect(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Ketik kode/nama produk...',
                });
            }

            let rowIndex = 1;
            window.addDetailRow = function() {
                const container = document.getElementById('detail-rows');
                const row = document.createElement('div');
                row.className = 'detail-input-row';
                row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;';

                const selectId = `produk_select_${rowIndex}`;
                row.innerHTML = `
                    <div class="form-group" style="margin-bottom:0;">
                        <select id="${selectId}" name="details[${rowIndex}][produk_id]" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            ${produkOptions.map(p => `<option value="${p.id}">${p.text}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="details[${rowIndex}][qty_masuk]" class="form-control" min="1" required placeholder="Qty">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="details[${rowIndex}][harga_beli]" class="form-control" min="0" step="100" required placeholder="Harga">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;" aria-label="Hapus baris">✕</button>
                `;
                container.appendChild(row);

                // Init Tom Select on the new select
                initProdukSelect(document.getElementById(selectId));
                rowIndex++;
            }

            window.removeRow = function(btn) {
                const row = btn.closest('.detail-input-row');
                // Destroy Tom Select instance before removing
                const select = row.querySelector('select');
                if (select && select.tomselect) select.tomselect.destroy();
                row.remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
