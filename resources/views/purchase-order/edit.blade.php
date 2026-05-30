<x-app-layout>
    <x-slot name="title">Edit Purchase Order</x-slot>

    <div class="card">
        <form method="POST" action="{{ route('purchase-order.update', $po) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $po->tanggal->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control tom-select" data-placeholder="Cari supplier..." required>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $po->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        @foreach(['draft','dikirim','sebagian','selesai'] as $st)
                        <option value="{{ $st }}" {{ $po->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h4 style="margin:1.5rem 0 1rem;font-weight:700;">Detail Item PO</h4>
            <div id="detail-rows">
                @foreach($po->detail as $i => $d)
                <div class="detail-input-row" style="display:grid;grid-template-columns:2fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;">
                    <div class="form-group" style="margin-bottom:0;">
                        @if($i === 0)<label>Produk</label>@endif
                        <select name="details[{{ $i }}][produk_id]" class="form-control produk-select" required>
                            @foreach($produk as $p)
                            <option value="{{ $p->id }}" {{ $d->produk_id == $p->id ? 'selected' : '' }}>{{ $p->kode_barang }} - {{ $p->nama_barang }} ({{ $p->variasi_barang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        @if($i === 0)<label>Jumlah</label>@endif
                        <input type="number" name="details[{{ $i }}][jumlah]" class="form-control" min="1" value="{{ $d->jumlah }}" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;">✕</button>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline btn-sm" onclick="addDetailRow()" style="margin-bottom:1.5rem;">+ Tambah Item</button>

            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('purchase-order.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produkOptions = @json($produk->map(fn($p) => ['id' => $p->id, 'text' => $p->kode_barang.' - '.$p->nama_barang.' ('.$p->variasi_barang.')']));

            document.querySelectorAll('.produk-select').forEach(initProdukSelect);

            function initProdukSelect(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Ketik kode/nama produk...',
                });
            }

            let rowIndex = {{ $po->detail->count() }};
            window.addDetailRow = function() {
                const container = document.getElementById('detail-rows');
                const row = document.createElement('div');
                row.className = 'detail-input-row';
                row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;';

                const selectId = `produk_select_${rowIndex}`;
                row.innerHTML = `
                    <div class="form-group" style="margin-bottom:0;">
                        <select id="${selectId}" name="details[${rowIndex}][produk_id]" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            ${produkOptions.map(p => `<option value="${p.id}">${p.text}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="details[${rowIndex}][jumlah]" class="form-control" min="1" required placeholder="Jumlah">
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)" style="height:38px;">✕</button>
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
