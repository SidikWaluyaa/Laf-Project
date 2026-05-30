<x-app-layout>
    <x-slot name="title">Riwayat Per Tanggal</x-slot>

    {{-- Date Range Selector --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <form method="GET" action="{{ route('riwayat-tanggal') }}">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;min-width:150px;">
                    <label>📅 Tanggal Awal</label>
                    <input type="date" name="dari" class="form-control" value="{{ $dari }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:150px;">
                    <label>📅 Tanggal Akhir</label>
                    <input type="date" name="sampai" class="form-control" value="{{ $sampai }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:180px;">
                    <label>🔍 Cari Produk</label>
                    <input type="text" name="search" class="form-control" placeholder="Ketik kode/nama produk..." value="{{ request('search') }}">
                </div>
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    <a href="{{ route('riwayat-tanggal') }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('riwayat-tanggal.pdf', array_merge(request()->all(), ['tab' => $tab])) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
        {{-- MASUK Card --}}
        <div style="background:linear-gradient(135deg,#1a56db,#1e40af);color:#fff;border-radius:12px;padding:1.25rem 1.5rem;">
            <p style="margin:0;font-size:.8rem;opacity:.75;text-transform:uppercase;letter-spacing:1px;">Total Masuk</p>
            <div style="display:flex;align-items:baseline;gap:1rem;margin-top:.5rem;">
                <span style="font-size:2rem;font-weight:800;">Rp {{ number_format($totalNilaiMasuk, 0, ',', '.') }}</span>
            </div>
            <p style="margin:.5rem 0 0;font-size:.78rem;opacity:.7;">{{ number_format($totalQtyMasuk) }} item • {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
        </div>
        {{-- KELUAR Card --}}
        <div style="background:linear-gradient(135deg,#dc2626,#b91c1c);color:#fff;border-radius:12px;padding:1.25rem 1.5rem;">
            <p style="margin:0;font-size:.8rem;opacity:.75;text-transform:uppercase;letter-spacing:1px;">Total Keluar</p>
            <div style="display:flex;align-items:baseline;gap:1rem;margin-top:.5rem;">
                <span style="font-size:2rem;font-weight:800;">Rp {{ number_format($totalNilaiKeluar, 0, ',', '.') }}</span>
            </div>
            <p style="margin:.5rem 0 0;font-size:.78rem;opacity:.7;">{{ number_format($totalQtyKeluar) }} item • {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div style="display:flex;gap:0;margin-bottom:0;">
        <a href="{{ route('riwayat-tanggal', array_merge(request()->all(), ['tab' => 'masuk'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'masuk' ? 'background:#dbeafe;color:#1e40af;border:1.5px solid #93c5fd;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            ↓ Riwayat Masuk ({{ number_format($totalQtyMasuk) }} qty)
        </a>
        <a href="{{ route('riwayat-tanggal', array_merge(request()->all(), ['tab' => 'keluar'])) }}"
           style="padding:.75rem 1.5rem;font-weight:700;font-size:.875rem;border-radius:10px 10px 0 0;text-decoration:none;
           {{ $tab === 'keluar' ? 'background:#fef2f2;color:#991b1b;border:1.5px solid #fca5a5;border-bottom:none;' : 'background:#f0f0f0;color:#666;border:1.5px solid transparent;' }}">
            ↑ Riwayat Keluar ({{ number_format($totalQtyKeluar) }} qty)
        </a>
    </div>

    {{-- Tab: MASUK --}}
    @if($tab === 'masuk')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #1a56db;">
        <div class="table-container">
            <table>
                <thead>
                    <tr style="background:#dbeafe;">
                        <th>Tanggal</th>
                        <th>Tipe Nota</th>
                        <th>Nama Supplier / Pengirim</th>
                        <th>Keterangan</th>
                        <th>Produk</th>
                        <th style="text-align:center;">QTY Masuk</th>
                        <th>Satuan</th>
                        <th>Diinput Oleh</th>
                        <th>Lokasi Simpan</th>
                        <th style="text-align:right;">HPP</th>
                        <th style="text-align:right;">HPP x QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatMasuk as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->barangMasuk->tanggal->format('d/m/Y') }}</td>
                        <td><span style="background:#dbeafe;color:#1e40af;padding:.2rem .5rem;border-radius:5px;font-size:.75rem;font-weight:600;">Barang Masuk</span></td>
                        <td><strong>{{ $r->barangMasuk->supplier->nama_supplier ?? '-' }}</strong></td>
                        <td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->barangMasuk->keterangan }}">
                            {{ $r->barangMasuk->keterangan ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $r->produk->kode_barang }}</strong><br>
                            <span style="font-size:.75rem;color:#666;">{{ $r->produk->nama_barang }} {{ $r->produk->variasi_barang ? '('.$r->produk->variasi_barang.')' : '' }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="background:#dbeafe;color:#1e40af;padding:.2rem .6rem;border-radius:6px;font-weight:800;">{{ $r->qty_masuk }}</span>
                        </td>
                        <td>{{ $r->produk->satuan }}</td>
                        <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</span></td>
                        <td style="text-align:right;white-space:nowrap;">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</td>
                        <td style="text-align:right;white-space:nowrap;font-weight:700;color:#1e40af;">Rp {{ number_format($r->qty_masuk * $r->harga_beli, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="11" style="text-align:center;padding:2rem;color:#666;">Tidak ada data masuk pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
                @if($riwayatMasuk->count() > 0)
                <tfoot>
                    <tr style="background:#dbeafe;font-weight:800;">
                        <td colspan="5" style="text-align:right;">TOTAL</td>
                        <td style="text-align:center;">{{ number_format($totalQtyMasuk) }}</td>
                        <td colspan="3"></td>
                        <td></td>
                        <td style="text-align:right;color:#1e40af;font-size:1rem;">Rp {{ number_format($totalNilaiMasuk, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatMasuk->links() }}</div>
    </div>
    @endif

    {{-- Tab: KELUAR --}}
    @if($tab === 'keluar')
    <div class="card" style="border-radius:0 10px 10px 10px;border-top:3px solid #dc2626;">
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
                        <th style="text-align:right;">HPP</th>
                        <th style="text-align:right;">HPP x QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatKeluar as $r)
                    <tr>
                        <td style="white-space:nowrap;">{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
                        <td><span style="background:#fef2f2;color:#991b1b;padding:.2rem .5rem;border-radius:5px;font-size:.75rem;font-weight:600;">{{ $r->penjualan->tipe_nota ?? 'Penjualan' }}</span></td>
                        <td><strong>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</strong></td>
                        <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
                        <td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $r->penjualan->keterangan }}">
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
                        <td style="text-align:right;white-space:nowrap;">Rp {{ number_format($r->hpp_snapshot, 0, ',', '.') }}</td>
                        <td style="text-align:right;white-space:nowrap;font-weight:700;color:#991b1b;">Rp {{ number_format($r->qty_keluar * $r->hpp_snapshot, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="12" style="text-align:center;padding:2rem;color:#666;">Tidak ada data keluar pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
                @if($riwayatKeluar->count() > 0)
                <tfoot>
                    <tr style="background:#fef2f2;font-weight:800;">
                        <td colspan="6" style="text-align:right;">TOTAL</td>
                        <td style="text-align:center;">{{ number_format($totalQtyKeluar) }}</td>
                        <td colspan="3"></td>
                        <td></td>
                        <td style="text-align:right;color:#991b1b;font-size:1rem;">Rp {{ number_format($totalNilaiKeluar, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $riwayatKeluar->links() }}</div>
    </div>
    @endif
</x-app-layout>
