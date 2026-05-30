@extends('pdf.layout')
@section('content')
<table>
    <thead>
        <tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Variasi</th><th>Kategori</th><th class="text-right">HPP</th><th class="text-center">Total Stok</th><th class="text-right">Nilai</th></tr>
    </thead>
    <tbody>
        @foreach($items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $item->kode_barang }}</strong></td>
            <td>{{ $item->nama_barang }}</td>
            <td>{{ $item->variasi_barang ?? '-' }}</td>
            <td>{{ $item->kategori }}</td>
            <td class="text-right">{{ number_format($item->hpp, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($item->total_stok) }}</td>
            <td class="text-right"><strong>{{ number_format($item->nilai, 0, ',', '.') }}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" class="text-right">TOTAL NILAI ASET</td>
            <td class="text-right"><strong>Rp {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
        </tr>
    </tfoot>
</table>
@endsection
