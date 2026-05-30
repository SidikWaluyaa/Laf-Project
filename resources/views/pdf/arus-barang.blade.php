@extends('pdf.layout')
@section('content')
<div style="display:table;width:100%;">
    {{-- Left: Top Masuk --}}
    <div style="display:table-cell;width:48%;vertical-align:top;padding-right:10px;">
        <h3 style="color:#0891b2;margin-bottom:6px;font-size:11px;">📦 Barang Paling Banyak di Re-Stok</h3>
        <table>
            <thead>
                <tr style="background:#0891b2;">
                    <th style="width:20px;">#</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Total Masuk</th>
                    <th class="text-center">Stok Terkini</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topMasuk as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item->produk->kode_barang ?? '-' }}</strong> — {{ $item->produk->nama_barang ?? '' }} {{ $item->produk->variasi_barang ? '('.$item->produk->variasi_barang.')' : '' }}</td>
                    <td class="text-center"><strong>{{ number_format($item->total_masuk) }}</strong></td>
                    <td class="text-center">{{ number_format($item->stok_terkini) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Right: Top Keluar --}}
    <div style="display:table-cell;width:48%;vertical-align:top;padding-left:10px;">
        <h3 style="color:#dc2626;margin-bottom:6px;font-size:11px;">🔥 Barang Paling Laris / Banyak Keluar</h3>
        <table>
            <thead>
                <tr style="background:#dc2626;">
                    <th style="width:20px;">#</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Total Keluar</th>
                    <th class="text-center">Stok Terkini</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topKeluar as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item->produk->kode_barang ?? '-' }}</strong> — {{ $item->produk->nama_barang ?? '' }} {{ $item->produk->variasi_barang ? '('.$item->produk->variasi_barang.')' : '' }}</td>
                    <td class="text-center"><strong>{{ number_format($item->total_keluar) }}</strong></td>
                    <td class="text-center">{{ number_format($item->stok_terkini) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
