<x-app-layout>
    <x-slot name="title">Riwayat Gudang</x-slot>

    {{-- Lokasi Selector --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <form method="GET" action="{{ route('riwayat-gudang') }}">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;min-width:200px;">
                    <label>📍 Lokasi / Gudang</label>
                    <select name="lokasi_id" class="form-control tom-select" data-placeholder="Pilih Lokasi..." onchange="this.form.submit()">
                        @foreach($lokasiList as $l)
                        <option value="{{ $l->id }}" {{ $lokasiId == $l->id ? 'selected' : '' }}>{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:180px;">
                    <label>🔍 Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik kode/nama produk..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:130px;">
                    <label>Dari</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:130px;">
                    <label>Sampai</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    <a href="{{ route('riwayat-gudang', ['lokasi_id' => $lokasiId]) }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('riwayat-gudang.pdf', array_merge(request()->all(), ['tab' => $tab, 'lokasi_id' => $lokasiId])) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Lokasi Header --}}
    @if($lokasiAktif)
    <div style="background:linear-gradient(135deg,#2F3E2F,#1a2a1a);color:#fff;border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h3 style="margin:0;font-size:1.25rem;font-weight:800;">📦 {{ $lokasiAktif->nama_lokasi }}</h3>
            <p style="margin:.25rem 0 0;font-size:.82rem;opacity:.7;">Riwayat pergerakan barang masuk & keluar gudang</p>
        </div>
        <div style="display:flex;gap:1.5rem;">
            <div style="text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#86efac;">{{ number_format($totalMasuk) }}</span>
                <span style="display:block;font-size:.7rem;opacity:.7;">Total Masuk</span>
            </div>
            <div style="text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#fca5a5;">{{ number_format($totalKeluar) }}</span>
                <span style="display:block;font-size:.7rem;opacity:.7;">Total Keluar</span>
            </div>
            <div style="text-align:center;">
                <span style="font-size:1.5rem;font-weight:800;color:#FFD700;">{{ number_format($totalMasuk - $totalKeluar) }}</span>
                <span style="display:block;font-size:.7rem;opacity:.7;">Selisih</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div style="display:flex;gap:0;margin-bottom:0;">
        <a href="{{ route('riwayat-gudang', array_merge(request()->all(), ['tab' => 'masuk'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'masuk' ? 'background:#d4edda;color:#155724;border:1.5px solid #c3e6cb;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            ↓ Masuk Gudang ({{ number_format($totalMasuk) }})
        </a>
        <a href="{{ route('riwayat-gudang', array_merge(request()->all(), ['tab' => 'keluar'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'keluar' ? 'background:#f8d7da;color:#721c24;border:1.5px solid #f5c6cb;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            ↑ Keluar Gudang ({{ number_format($totalKeluar) }})
        </a>
    </div>

    {{-- Tab Content: MASUK --}}
    @if($tab === 'masuk')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #28a745;">
        <div class="table-container">
            <table>
                <thead>
                    <tr style="background:#d4edda;">
                        <th>Tanggal</th>
                        <th>Tipe Nota</th>
                        <th>Nama Supplier / Pengirim</th>
                        <th>Keterangan</th>
                        <th>Produk</th>
                        <th style="text-align:center;">QTY Masuk</th>
                        <th>Satuan</th>
                        <th style="text-align:right;">Harga Beli</th>
                        <th>Diinput Oleh</th>
                        <th>Lokasi Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatMasuk as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->barangMasuk->tanggal->format('d/m/Y') }}</td>
                        <td><span class="badge badge-success">Barang Masuk</span></td>
                        <td><strong>{{ $r->barangMasuk->supplier->nama_supplier ?? '-' }}</strong></td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->barangMasuk->keterangan }}">
                            {{ $r->barangMasuk->keterangan ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $r->produk->kode_barang }}</strong><br>
                            <span style="font-size:.78rem;color:#666;">{{ $r->produk->nama_barang }} {{ $r->produk->variasi_barang ? '('.$r->produk->variasi_barang.')' : '' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#d4edda;color:#155724;padding:.25rem .7rem;border-radius:6px;font-weight:800;font-size:.9rem;">
                                +{{ $r->qty_masuk }}
                            </span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td style="text-align:right;white-space:nowrap;">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</td>
                        <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:2rem;color:#666;">Belum ada riwayat barang masuk ke lokasi ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatMasuk->links() }}</div>
    </div>
    @endif

    {{-- Tab Content: KELUAR --}}
    @if($tab === 'keluar')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #dc3545;">
        <div class="table-container">
            <table>
                <thead>
                    <tr style="background:#f8d7da;">
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
                    @forelse($riwayatKeluar as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
                        <td><span class="badge badge-warning">{{ $r->penjualan->tipe_nota ?? 'Penjualan' }}</span></td>
                        <td><strong>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</strong></td>
                        <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
                        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->penjualan->keterangan }}">
                            {{ $r->penjualan->keterangan ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $r->produk->kode_barang }}</strong><br>
                            <span style="font-size:.78rem;color:#666;">{{ $r->produk->nama_barang }} {{ $r->produk->variasi_barang ? '('.$r->produk->variasi_barang.')' : '' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#f8d7da;color:#721c24;padding:.25rem .7rem;border-radius:6px;font-weight:800;font-size:.9rem;">
                                -{{ $r->qty_keluar }}
                            </span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td>{{ $r->penjualan->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-danger">{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:2rem;color:#666;">Belum ada riwayat barang keluar dari lokasi ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatKeluar->links() }}</div>
    </div>
    @endif
</x-app-layout>
