@extends('pdf.layout')
@section('content')
<table>
    <thead>
        <tr><th>No</th><th>Tanggal</th><th>Nota</th><th>Pelanggan</th><th>Kode</th><th>Produk</th><th class="text-center">QTY</th><th class="text-right">HPP</th><th class="text-right">Subtotal</th><th>Lokasi</th><th>Admin</th></tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
            <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
            <td>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</td>
            <td><strong>{{ $r->produk->kode_barang }}</strong></td>
            <td>{{ $r->produk->nama_barang }}</td>
            <td class="text-center"><strong>{{ $r->qty_keluar }}</strong></td>
            <td class="text-right">{{ number_format($r->hpp_snapshot, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($r->qty_keluar * $r->hpp_snapshot, 0, ',', '.') }}</td>
            <td>{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</td>
            <td>{{ $r->penjualan->admin->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ number_format($totalQty) }}</strong></td>
            <td></td>
            <td class="text-right"><strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endsection
