@extends('pdf.layout')
@section('content')
@if($tab === 'masuk')
<h3 style="color:#155724;margin-bottom:8px;">↓ Riwayat Masuk Gudang</h3>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Supplier</th>
            <th>Keterangan</th>
            <th>Produk</th>
            <th class="text-center">QTY Masuk</th>
            <th>Satuan</th>
            <th class="text-right">Harga Beli</th>
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
            <td class="text-center"><span class="badge badge-green">{{ $r->qty_masuk }}</span></td>
            <td>{{ $r->produk->satuan }}</td>
            <td class="text-right">{{ number_format($r->harga_beli, 0, ',', '.') }}</td>
            <td>{{ $r->barangMasuk->admin->name ?? '-' }}</td>
            <td>{{ $r->barangMasuk->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">TOTAL</td>
            <td class="text-center"><strong>{{ number_format($totalQty) }}</strong></td>
            <td colspan="4"></td>
        </tr>
    </tfoot>
</table>
@else
<h3 style="color:#991b1b;margin-bottom:8px;">↑ Riwayat Keluar Gudang</h3>
<table>
    <thead>
        <tr style="background:#991b1b;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>No Nota</th>
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
            <td><strong>{{ $r->produk->kode_barang }}</strong> — {{ $r->produk->nama_barang }}</td>
            <td class="text-center"><span class="badge badge-red">{{ $r->qty_keluar }}</span></td>
            <td>{{ $r->produk->satuan }}</td>
            <td>{{ $r->penjualan->admin->name ?? '-' }}</td>
            <td>{{ $r->penjualan->lokasi->nama_lokasi ?? '-' }}</td>
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
@endif
@endsection
