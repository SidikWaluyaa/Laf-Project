@extends('pdf.layout')
@section('content')
<table>
    <thead>
        <tr><th>No</th><th>Kode Produk</th><th>Nama Produk</th><th>Variasi</th><th class="text-center">Jumlah PO</th><th class="text-center">Diterima</th><th class="text-center">Sisa</th></tr>
    </thead>
    <tbody>
        @foreach($po->detail as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $d->produk->kode_barang }}</strong></td>
            <td>{{ $d->produk->nama_barang }}</td>
            <td>{{ $d->produk->variasi_barang ?? '-' }}</td>
            <td class="text-center"><strong>{{ $d->jumlah }}</strong></td>
            <td class="text-center">{{ $d->barang_masuk }}</td>
            <td class="text-center">{{ $d->sisa }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ $po->detail->sum('jumlah') }}</strong></td>
            <td class="text-center">{{ $po->detail->sum('barang_masuk') }}</td>
            <td class="text-center">{{ $po->detail->sum('sisa') }}</td>
        </tr>
    </tfoot>
</table>
@endsection
