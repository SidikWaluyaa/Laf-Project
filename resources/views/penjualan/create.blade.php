<x-app-layout>
    <x-slot name="title">Input Penjualan</x-slot>

    <div class="card">
        <form method="POST" action="{{ route('penjualan.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="tipe_nota">Tipe Nota</label>
                    <input type="text" id="tipe_nota" name="tipe_nota" class="form-control" value="{{ old('tipe_nota') }}" placeholder="Cash / Transfer">
                </div>
                <div class="form-group">
                    <label for="nomor_nota">Nomor Nota (Otomatis)</label>
                    <input type="text" id="nomor_nota" name="nomor_nota" class="form-control" value="{{ $autoNomorNota }}" readonly style="background-color: var(--bg-color); font-weight: 600;">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="pelanggan_id">Pelanggan</label>
                    <select id="pelanggan_id" name="pelanggan_id" class="form-control tom-select" data-placeholder="Cari pelanggan...">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelanggan as $pl)
                        <option value="{{ $pl->id }}" {{ old('pelanggan_id') == $pl->id ? 'selected' : '' }}>{{ $pl->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="lokasi_id">Lokasi</label>
                    <select id="lokasi_id" name="lokasi_id" class="form-control tom-select" data-placeholder="Cari lokasi..." required>
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasi as $l)
                        <option value="{{ $l->id }}" {{ old('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Opsional">
            </div>

            <h4 style="margin:1.5rem 0 1rem;font-weight:700;">Detail Item</h4>
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
                        <label>Qty Keluar</label>
                        <input type="number" name="details[0][qty_keluar]" class="form-control qty-input" min="1" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-btn" style="height:38px;" aria-label="Hapus baris">✕</button>
                    <div class="stock-warning" style="grid-column: 1 / -1; font-size: 0.85rem; padding-top: 4px;"></div>
                </div>
            </div>

            <button type="button" class="btn btn-outline btn-sm" onclick="addDetailRow()" style="margin-bottom:1.5rem;">+ Tambah Item</button>

            <div style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('penjualan.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produkOptions = @json($produk->map(fn($p) => ['id' => $p->id, 'text' => $p->kode_barang.' - '.$p->nama_barang.' ('.$p->variasi_barang.')']));
            const stokMap = @json($stokMap ?? []);
            const lokasiSelect = document.getElementById('lokasi_id');
            const submitBtn = document.querySelector('button[type="submit"]');
            const container = document.getElementById('detail-rows');

            document.querySelectorAll('.produk-select').forEach(initProdukSelect);

            function initProdukSelect(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Ketik kode/nama produk...',
                    onChange: function() {
                        validateStock(el);
                    }
                });
            }

            let rowIndex = 1;
            window.addDetailRow = function() {
                const row = document.createElement('div');
                row.className = 'detail-input-row';
                row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem;';

                const selectId = `produk_select_${rowIndex}`;
                row.innerHTML = `
                    <div class="form-group" style="margin-bottom:0;">
                        <select id="${selectId}" name="details[${rowIndex}][produk_id]" class="form-control produk-select" required>
                            <option value="">-- Pilih Produk --</option>
                            ${produkOptions.map(p => `<option value="${p.id}">${p.text}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="details[${rowIndex}][qty_keluar]" class="form-control qty-input" placeholder="Qty" min="1" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-btn" style="height:38px;">✕</button>
                    <div class="stock-warning" style="grid-column: 1 / -1; font-size: 0.85rem; padding-top: 4px;"></div>
                `;

                container.appendChild(row);
                initProdukSelect(document.getElementById(selectId));
                rowIndex++;
                checkFormValidity();
            };

            // Event Delegation for dynamic rows
            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input')) {
                    validateStock(e.target);
                }
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-btn')) {
                    const row = e.target.closest('.detail-input-row');
                    const select = row.querySelector('select');
                    if (select && select.tomselect) select.tomselect.destroy();
                    row.remove();
                    validateAllStock();
                }
            });

            // Listen to Location change
            if (lokasiSelect) {
                lokasiSelect.addEventListener('change', validateAllStock);
                
                // Fallback for global tom-select initialized on lokasi_id
                setTimeout(() => {
                    if (lokasiSelect.tomselect) {
                        lokasiSelect.tomselect.on('change', validateAllStock);
                    } else if (typeof jQuery !== 'undefined') {
                        jQuery(lokasiSelect).on('change', validateAllStock);
                    }
                }, 500);
            }

            function validateAllStock() {
                document.querySelectorAll('.detail-input-row').forEach(row => {
                    const input = row.querySelector('.qty-input');
                    if (input) validateStock(input);
                });
                checkFormValidity();
            }

            function validateStock(element) {
                const row = element.closest('.detail-input-row');
                if (!row) return;
                
                const produkSelect = row.querySelector('select[name^="details"]');
                const qtyInput = row.querySelector('.qty-input');
                const warningDiv = row.querySelector('.stock-warning');
                
                const lokasiId = lokasiSelect ? lokasiSelect.value : null;
                const produkId = produkSelect ? produkSelect.value : null;
                
                // Reset styling
                if (qtyInput) qtyInput.style.borderColor = '';
                if (warningDiv) warningDiv.innerHTML = '';
                
                if (!lokasiId || !produkId) {
                    checkFormValidity();
                    return;
                }

                const availableStock = (stokMap[lokasiId] && stokMap[lokasiId][produkId]) ? stokMap[lokasiId][produkId] : 0;
                const requestedQty = parseInt(qtyInput ? qtyInput.value : 0) || 0;

                if (warningDiv) {
                    if (availableStock === 0) {
                        warningDiv.innerHTML = `<span style="color: #e3342f;"><i class="fas fa-times-circle"></i> Stok kosong di lokasi ini!</span>`;
                        if (qtyInput) qtyInput.style.borderColor = '#e3342f';
                    } else if (requestedQty > availableStock) {
                        warningDiv.innerHTML = `<span style="color: #e3342f;"><i class="fas fa-exclamation-triangle"></i> Melebihi stok! (Maks: ${availableStock})</span>`;
                        if (qtyInput) qtyInput.style.borderColor = '#e3342f';
                    } else {
                        warningDiv.innerHTML = `<span style="color: #38c172;"><i class="fas fa-check-circle"></i> Sisa stok di lokasi ini: <b>${availableStock}</b></span>`;
                    }
                }
                
                checkFormValidity();
            }

            function checkFormValidity() {
                let hasError = false;
                document.querySelectorAll('.detail-input-row').forEach(row => {
                    const produkSelect = row.querySelector('select[name^="details"]');
                    const qtyInput = row.querySelector('.qty-input');
                    
                    const produkId = produkSelect ? produkSelect.value : null;
                    const requestedQty = parseInt(qtyInput ? qtyInput.value : 0) || 0;
                    const lokasiId = lokasiSelect ? lokasiSelect.value : null;
                    
                    if (!lokasiId || !produkId || requestedQty <= 0) {
                        hasError = true;
                        return;
                    }

                    const availableStock = (stokMap[lokasiId] && stokMap[lokasiId][produkId]) ? stokMap[lokasiId][produkId] : 0;
                    if (requestedQty > availableStock || availableStock === 0) {
                        hasError = true;
                    }
                });
                
                if (submitBtn) {
                    submitBtn.disabled = hasError;
                    if(hasError) {
                        submitBtn.innerHTML = 'Stok Tidak Cukup / Data Belum Lengkap';
                        submitBtn.classList.remove('btn-primary');
                        submitBtn.classList.add('btn-danger');
                    } else {
                        submitBtn.innerHTML = 'Simpan';
                        submitBtn.classList.remove('btn-danger');
                        submitBtn.classList.add('btn-primary');
                    }
                }
            }

            // Initial validation on page load
            setTimeout(validateAllStock, 300);
        });
    </script>
    @endpush
</x-app-layout>
