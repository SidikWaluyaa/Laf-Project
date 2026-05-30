@extends('pdf.layout')
@section('content')
@if($tab === 'supplier')
<h3 style="color:#3730a3;margin-bottom:8px;">🚚 Riwayat Supplier: {{ $entityName }}</h3>
<table>
    <thead>
        <tr style="background:#3730a3;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Keterangan</th>
            <th>Produk</th>
            <th class="text-center">QTY Masuk</th>
            <th>Satuan</th>
            <th>Admin</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->barangMasuk->tanggal->format('d/m/Y') }}</td>
            <td>{{ $r->barangMasuk->supplier->nama_supplier ?? '-' }}</td>
            <td>{{ Str::limit($r->barangMasuk->keterangan, 20) ?? '-' }}</td>
            <td><strong>{{ $r->produk->kode_barang }}</strong> — {{ $r->produk->nama_barang }}</td>
            <td class="text-center"><strong>{{ $r->qty_masuk }}</strong></td>
            <td>{{ $r->produk->satuan }}</td>
            <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
            <td>{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ number_format($totalQty) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@else
<h3 style="color:#991b1b;margin-bottom:8px;">👤 Riwayat Pelanggan: {{ $entityName }}</h3>
<table>
    <thead>
        <tr style="background:#991b1b;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>No Nota</th>
            <th>Keterangan</th>
            <th>Produk</th>
            <th class="text-center">QTY Keluar</th>
            <th>Satuan</th>
            <th>Admin</th>
            <th>Lokasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->penjualan->tanggal->format('d/m/Y') }}</td>
            <td>{{ $r->penjualan->pelanggan->nama_pelanggan ?? '-' }}</td>
            <td>{{ $r->penjualan->nomor_nota ?? '-' }}</td>
            <td>{{ Str::limit($r->penjualan->keterangan, 18) ?? '-' }}</td>
            <td><strong>{{ $r->produk->kode_barang }}</strong> — {{ $r->produk->nama_barang }}</td>
            <td class="text-center"><strong>{{ $r->qty_keluar }}</strong></td>
            <td>{{ $r->produk->satuan }}</td>
            <td>{{ $r->penjualan->admin->name ?? '-' }}</td>
            <td>{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ number_format($totalQty) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@endif
@endsection
