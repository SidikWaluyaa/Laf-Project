@extends('pdf.layout')
@section('content')
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Keterangan</th>
            <th>Kode Produk</th>
            <th>Nama Produk</th>
            <th>Variasi</th>
            <th class="text-center">QTY</th>
            <th>Satuan</th>
            <th class="text-right">Harga Beli</th>
            <th class="text-right">Subtotal</th>
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
            <td><strong>{{ $r->produk->kode_barang }}</strong></td>
            <td>{{ $r->produk->nama_barang }}</td>
            <td>{{ $r->produk->variasi_barang ?? '-' }}</td>
            <td class="text-center"><strong>{{ $r->qty_masuk }}</strong></td>
            <td>{{ $r->produk->satuan }}</td>
            <td class="text-right">{{ number_format($r->harga_beli, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($r->qty_masuk * $r->harga_beli, 0, ',', '.') }}</td>
            <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
            <td>{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ number_format($totalQty) }}</strong></td>
            <td></td>
            <td></td>
            <td class="text-right"><strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@endsection
