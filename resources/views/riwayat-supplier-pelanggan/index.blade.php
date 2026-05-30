<x-app-layout>
    <x-slot name="title">Riwayat Supplier & Pelanggan</x-slot>

    {{-- Tab Navigation --}}
    <div style="display:flex;gap:0;margin-bottom:0;">
        <a href="{{ route('riwayat-supplier-pelanggan', array_merge(request()->only(['supplier_id','search','dari','sampai']), ['tab' => 'supplier'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'supplier' ? 'background:#e0e7ff;color:#3730a3;border:1.5px solid #a5b4fc;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            🚚 Riwayat Supplier ({{ number_format($totalQtySupplier) }} qty)
        </a>
        <a href="{{ route('riwayat-supplier-pelanggan', array_merge(request()->only(['pelanggan_id','search','dari','sampai']), ['tab' => 'pelanggan'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'pelanggan' ? 'background:#fef2f2;color:#991b1b;border:1.5px solid #fca5a5;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            👤 Riwayat Pelanggan ({{ number_format($totalQtyPelanggan) }} qty)
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════
         TAB: SUPPLIER
    ═══════════════════════════════════════════════════ --}}
    @if($tab === 'supplier')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #4f46e5;margin-bottom:0;">
        {{-- Filter --}}
        <form method="GET" action="{{ route('riwayat-supplier-pelanggan') }}" style="margin-bottom:1.25rem;">
            <input type="hidden" name="tab" value="supplier">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;min-width:220px;">
                    <label>🚚 Pilih Supplier</label>
                    <select name="supplier_id" class="form-control tom-select" data-placeholder="Cari supplier..." onchange="this.form.submit()">
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $supplierId == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px;">
                    <label>🔍 Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik kode/nama..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:120px;">
                    <label>Dari</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:120px;">
                    <label>Sampai</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('riwayat-supplier-pelanggan', ['tab' => 'supplier', 'supplier_id' => $supplierId]) }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('riwayat-supplier-pelanggan.pdf', array_merge(request()->all(), ['tab' => 'supplier', 'supplier_id' => $supplierId])) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>

        {{-- Supplier Header --}}
        @if($supplierAktif)
        <div style="background:linear-gradient(135deg,#4338ca,#3730a3);color:#fff;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div>
                <h3 style="margin:0;font-size:1.15rem;font-weight:800;">🚚 {{ $supplierAktif->nama_supplier }}</h3>
                <p style="margin:.2rem 0 0;font-size:.78rem;opacity:.7;">Riwayat semua barang masuk dari supplier ini</p>
            </div>
            <div style="text-align:center;">
                <span style="font-size:1.75rem;font-weight:800;color:#c7d2fe;">{{ number_format($totalQtySupplier) }}</span>
                <span style="display:block;font-size:.7rem;opacity:.7;">Total Qty Masuk</span>
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr style="background:#e0e7ff;">
                        <th>Tanggal</th>
                        <th>Tipe Nota</th>
                        <th>Nama Supplier</th>
                        <th>Keterangan</th>
                        <th>Produk</th>
                        <th style="text-align:center;">QTY Masuk</th>
                        <th>Satuan</th>
                        <th>Diinput Oleh</th>
                        <th>Lokasi Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatSupplier as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->barangMasuk->tanggal->format('d/m/Y') }}</td>
                        <td><span style="background:#e0e7ff;color:#3730a3;padding:.2rem .5rem;border-radius:5px;font-size:.75rem;font-weight:600;">Barang Masuk</span></td>
                        <td><strong>{{ $r->barangMasuk->supplier->nama_supplier ?? '-' }}</strong></td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->barangMasuk->keterangan }}">
                            {{ $r->barangMasuk->keterangan ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $r->produk->kode_barang }}</strong><br>
                            <span style="font-size:.75rem;color:#666;">{{ $r->produk->nama_barang }} {{ $r->produk->variasi_barang ? '('.$r->produk->variasi_barang.')' : '' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#e0e7ff;color:#3730a3;padding:.2rem .6rem;border-radius:6px;font-weight:800;">{{ $r->qty_masuk }}</span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:2rem;color:#666;">Belum ada riwayat barang masuk dari supplier ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatSupplier->links() }}</div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════
         TAB: PELANGGAN
    ═══════════════════════════════════════════════════ --}}
    @if($tab === 'pelanggan')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #dc2626;margin-bottom:0;">
        {{-- Filter --}}
        <form method="GET" action="{{ route('riwayat-supplier-pelanggan') }}" style="margin-bottom:1.25rem;">
            <input type="hidden" name="tab" value="pelanggan">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;min-width:220px;">
                    <label>👤 Pilih Pelanggan</label>
                    <select name="pelanggan_id" class="form-control tom-select" data-placeholder="Cari pelanggan..." onchange="this.form.submit()">
                        @foreach($pelangganList as $pl)
                        <option value="{{ $pl->id }}" {{ $pelangganId == $pl->id ? 'selected' : '' }}>{{ $pl->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px;">
                    <label>🔍 Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik kode/nama..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:120px;">
                    <label>Dari</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:120px;">
                    <label>Sampai</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('riwayat-supplier-pelanggan', ['tab' => 'pelanggan', 'pelanggan_id' => $pelangganId]) }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('riwayat-supplier-pelanggan.pdf', array_merge(request()->all(), ['tab' => 'pelanggan', 'pelanggan_id' => $pelangganId])) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>

        {{-- Pelanggan Header --}}
        @if($pelangganAktif)
        <div style="background:linear-gradient(135deg,#dc2626,#991b1b);color:#fff;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <div>
                <h3 style="margin:0;font-size:1.15rem;font-weight:800;">👤 {{ $pelangganAktif->nama_pelanggan }}</h3>
                <p style="margin:.2rem 0 0;font-size:.78rem;opacity:.7;">Riwayat semua penjualan ke pelanggan ini</p>
            </div>
            <div style="text-align:center;">
                <span style="font-size:1.75rem;font-weight:800;color:#fca5a5;">{{ number_format($totalQtyPelanggan) }}</span>
                <span style="display:block;font-size:.7rem;opacity:.7;">Total Qty Keluar</span>
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr style="background:#fef2f2;">
                        <th>Tanggal</th>
                        <th>Tipe Nota</th>
                        <th>Nama Pelanggan</th>
                        <th>No Nota</th>
                        <th>Keterangan</th>
                        <th>Produk</th>
                        <th style="text-align:center;">QTY Keluar</th>
                        <th>Satuan</th>
                        <th>Diinput Oleh</th>
                        <th>Lokasi Pengambilan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPelanggan as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
                        <td><span style="background:#fef2f2;color:#991b1b;padding:.2rem .5rem;border-radius:5px;font-size:.75rem;font-weight:600;">{{ $r->penjualan->tipe_nota ?? 'Penjualan' }}</span></td>
                        <td><strong>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</strong></td>
                        <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->penjualan->keterangan }}">
                            {{ $r->penjualan->keterangan ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $r->produk->kode_barang }}</strong><br>
                            <span style="font-size:.75rem;color:#666;">{{ $r->produk->nama_barang }} {{ $r->produk->variasi_barang ? '('.$r->produk->variasi_barang.')' : '' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#fef2f2;color:#991b1b;padding:.2rem .6rem;border-radius:6px;font-weight:800;">{{ $r->qty_keluar }}</span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td>{{ $r->penjualan->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-danger">{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:2rem;color:#666;">Belum ada riwayat penjualan ke pelanggan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatPelanggan->links() }}</div>
    </div>
    @endif
</x-app-layout>
