<x-app-layout>
    <x-slot name="title">Arus Barang</x-slot>

    {{-- Filter --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <form method="GET" action="{{ route('arus-barang') }}">
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;min-width:140px;">
                    <label>📅 Dari</label>
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:140px;">
                    <label>📅 Sampai</label>
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;min-width:90px;">
                    <label>Top</label>
                    <select name="limit" class="form-control">
                        @foreach([10, 25, 50, 100] as $opt)
                        <option value="{{ $opt }}" {{ $limit == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:.5rem;">
                    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    <a href="{{ route('arus-barang') }}" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </div>
        </form>
        <div style="margin-top:.75rem;text-align:right;">
            <a href="{{ route('arus-barang.pdf', request()->all()) }}" class="btn btn-sm" style="background:#dc2626;color:#fff;gap:.3rem;display:inline-flex;align-items:center;" target="_blank">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Two-Column Layout --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

        {{-- ═══════════════════════════════════════
             LEFT: Barang Paling Banyak di Re-Stok
        ═══════════════════════════════════════ --}}
        <div>
            <div style="background:linear-gradient(135deg,#06b6d4,#0891b2);color:#fff;border-radius:12px 12px 0 0;padding:1rem 1.25rem;text-align:center;">
                <h3 style="margin:0;font-size:1.1rem;font-weight:800;">📦 Barang Paling Banyak di Re-Stok</h3>
                <p style="margin:.25rem 0 0;font-size:.72rem;opacity:.75;">Diurutkan dari yang paling banyak Masuk</p>
            </div>
            <div class="card" style="border-radius:0 0 12px 12px;border-top:none;margin-bottom:0;">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr style="background:#cffafe;">
                                <th style="width:30px;">#</th>
                                <th>Nama Barang</th>
                                <th style="text-align:center;color:#0891b2;">Stok Paling Banyak Masuk</th>
                                <th style="text-align:center;">Stok Terkini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMasuk as $i => $item)
                            <tr>
                                <td style="font-weight:700;color:#0891b2;">{{ $i + 1 }}</td>
                                <td>
                                    <strong>{{ $item->produk->kode_barang ?? '-' }}</strong><br>
                                    <span style="font-size:.75rem;color:#666;">{{ $item->produk->nama_barang ?? '' }} {{ $item->produk->variasi_barang ? '('.$item->produk->variasi_barang.')' : '' }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span style="background:#cffafe;color:#0e7490;padding:.25rem .7rem;border-radius:6px;font-weight:800;font-size:.95rem;">
                                        {{ number_format($item->total_masuk) }}
                                    </span>
                                </td>
                                <td style="text-align:center;font-weight:700;">{{ number_format($item->stok_terkini) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:#666;">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             RIGHT: Barang Paling Laris / Keluar
        ═══════════════════════════════════════ --}}
        <div>
            <div style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;border-radius:12px 12px 0 0;padding:1rem 1.25rem;text-align:center;">
                <h3 style="margin:0;font-size:1.1rem;font-weight:800;">🔥 Barang Paling Laris / Banyak Keluar</h3>
                <p style="margin:.25rem 0 0;font-size:.72rem;opacity:.75;">Diurutkan dari yang paling banyak Keluar</p>
            </div>
            <div class="card" style="border-radius:0 0 12px 12px;border-top:none;margin-bottom:0;">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr style="background:#fef2f2;">
                                <th style="width:30px;">#</th>
                                <th>Nama Barang</th>
                                <th style="text-align:center;color:#dc2626;">Stok Paling Banyak Keluar</th>
                                <th style="text-align:center;">Stok Terkini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topKeluar as $i => $item)
                            <tr>
                                <td style="font-weight:700;color:#dc2626;">{{ $i + 1 }}</td>
                                <td>
                                    <strong>{{ $item->produk->kode_barang ?? '-' }}</strong><br>
                                    <span style="font-size:.75rem;color:#666;">{{ $item->produk->nama_barang ?? '' }} {{ $item->produk->variasi_barang ? '('.$item->produk->variasi_barang.')' : '' }}</span>
                                </td>
                                <td style="text-align:center;">
                                    <span style="background:#fef2f2;color:#dc2626;padding:.25rem .7rem;border-radius:6px;font-weight:800;font-size:.95rem;">
                                        {{ number_format($item->total_keluar) }}
                                    </span>
                                </td>
                                <td style="text-align:center;font-weight:700;">{{ number_format($item->stok_terkini) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:#666;">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns:1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    @endpush
</x-app-layout>
