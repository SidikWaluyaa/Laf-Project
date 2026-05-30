📊 *REKAPITULASI SHIFT HARI INI* 📊
📅 Tanggal: {{ $tanggal }}
👤 Dilaporkan: {{ $pelapor }}

📈 *PENJUALAN (BARANG KELUAR):*
Nota Beredar: {{ $totalNotaSales }} Transaksi
Total Omzet: *Rp {{ number_format($omzetHariIni, 0, ',', '.') }}*
@if($batalPenjualan > 0)
_(Dibatalkan/VOID: {{ $batalPenjualan }} trx)_
@endif

@if($rincianPenjualan->isNotEmpty())
*🛍️ Rincian Terjual:*
@php
    $sumQty = 0;
    $sumTotal = 0;
@endphp
@foreach($rincianPenjualan as $idx => $rp)
@php
    $sumQty += $rp->total_qty;
    $sumTotal += $rp->subtotal;
    $sub = number_format($rp->subtotal, 0, ',', '.');
@endphp
{{ $idx + 1 }}. {{ $rp->nama_barang }} • {{ $rp->total_qty }}x (Rp {{ $sub }})
@endforeach
-----------------------------------
*Total: {{ $sumQty }} item (Rp {{ number_format($sumTotal, 0, ',', '.') }})*
@endif

📉 *BELANJA (BARANG MASUK):*
Nota Beredar: {{ $totalNotaGM }} Transaksi
Total Pengeluaran: *Rp {{ number_format($pengeluaranHariIni, 0, ',', '.') }}*
@if($batalMasuk > 0)
_(Dibatalkan/VOID: {{ $batalMasuk }} trx)_
@endif

@if($rincianBelanja->isNotEmpty())
*📦 Rincian Dibeli:*
@php
    $sumQtyB = 0;
    $sumTotalB = 0;
@endphp
@foreach($rincianBelanja as $idx => $rb)
@php
    $sumQtyB += $rb->total_qty;
    $sumTotalB += $rb->subtotal;
    $sub = number_format($rb->subtotal, 0, ',', '.');
@endphp
{{ $idx + 1 }}. {{ $rb->nama_barang }} • {{ $rb->total_qty }}x (Rp {{ $sub }})
@endforeach
-----------------------------------
*Total: {{ $sumQtyB }} item (Rp {{ number_format($sumTotalB, 0, ',', '.') }})*
@endif

💰 *SNAPSHOT ASET GUDANG*
Estimasi Total Aset: *Rp {{ number_format($totalAssetValue, 0, ',', '.') }}*

Selamat beristirahat! ☕
