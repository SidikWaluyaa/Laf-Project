<x-app-layout>
    <x-slot name="title">Buat Purchase Order</x-slot>

    <div class="card">
        <form method="POST" action="{{ route('purchase-order.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="nomor_po">Nomor PO (Otomatis)</label>
                    <input type="text" id="nomor_po" name="nomor_po" class="form-control" value="{{ $autoNomorPo }}" readonly style="background-color: var(--bg-color); font-weight: 600;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="tanggal">Tanggal Rilis</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label for="supplier_id">PO Kepada (Supplier)</label>
                    <select id="supplier_id" name="supplier_id" class="form-control tom-select" data-placeholder="Cari supplier..." required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h4 style="margin:1.5rem 0 1rem;font-weight:700;">Detail Item PO</h4>
            <div id="detail-rows">
                <div class="detail-input-row" style="display:grid;grid-template-columns:2fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;">
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
                        <label>Jumlah</label>
                        <input type="number" name="details[0][jumlah]" class="form-control" min="1" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;" aria-label="Hapus baris">✕</button>
                </div>
            </div>

            <button type="button" class="btn btn-outline btn-sm" onclick="addDetailRow()" style="margin-bottom:1.5rem;">+ Tambah Item</button>

            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('purchase-order.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produkOptions = @json($produk->map(fn($p) => ['id' => $p->id, 'text' => $p->kode_barang.' - '.$p->nama_barang.' ('.$p->variasi_barang.')', 'hpp' => $p->hpp]));

            document.querySelectorAll('.produk-select').forEach(initProdukSelect);

            function initProdukSelect(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Ketik kode/nama produk...',
                    onChange: function(value) {
                        const row = this.wrapper.closest('.detail-input-row');
                        const hppInput = row.querySelector('.harga-input');
                        const selected = produkOptions.find(p => p.id == value);
                        if(selected && hppInput) {
                            hppInput.value = selected.hpp;
                        }
                    }
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
                        <input type="number" name="details[${rowIndex}][jumlah]" class="form-control" min="1" required placeholder="Qty">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="details[${rowIndex}][harga_satuan]" class="form-control harga-input" min="0" required placeholder="Harga (Rp)">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;" aria-label="Hapus baris">✕</button>
                `;
                container.appendChild(row);
                initProdukSelect(document.getElementById(selectId));
                rowIndex++;
            }

            window.removeRow = function(btn) {
                const row = btn.closest('.detail-input-row');
                const select = row.querySelector('select');
                if (select && select.tomselect) select.tomselect.destroy();
                row.remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
