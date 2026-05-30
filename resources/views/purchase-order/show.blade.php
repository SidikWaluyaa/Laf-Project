<x-app-layout>
    <x-slot name="title">Detail PO</x-slot>

    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;">
        <a href="{{ route('purchase-order.index') }}" class="btn btn-outline btn-sm">← Kembali</a>
        <a href="{{ route('purchase-order.pdf', $po) }}" class="btn btn-primary btn-sm" target="_blank">📄 Cetak PDF</a>
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h3>Informasi PO</h3></div>
        <dl>
            <div class="detail-row"><dt>Nomor PO</dt><dd><strong>{{ $po->nomor_po ?? '-' }}</strong></dd></div>
            <div class="detail-row"><dt>Tanggal</dt><dd>{{ $po->tanggal ? $po->tanggal->format('d/m/Y') : '-' }}</dd></div>
            <div class="detail-row"><dt>Supplier</dt><dd>{{ $po->supplier->nama_supplier ?? '-' }}</dd></div>
            <div class="detail-row">
                <dt>Status</dt>
                <dd>
                    @php $sc = match($po->status) { 'selesai' => 'badge-success', 'sebagian' => 'badge-warning', 'dikirim' => 'badge-info', default => 'badge-secondary' }; @endphp
                    <span class="badge {{ $sc }}">{{ ucfirst($po->status) }}</span>
                </dd>
            </div>
        </dl>
    </div>

    <div class="card">
        <div class="card-header"><h3>Detail Item</h3></div>
        <div class="table-container">
            <table>
                <thead><tr><th>Produk</th><th>Jumlah PO</th><th>Barang Masuk</th><th>Sisa</th><th>Progress</th></tr></thead>
                <tbody>
                    @foreach($po->detail as $d)
                    <tr>
                        <td>{{ $d->produk->kode_barang ?? '' }} - {{ $d->produk->nama_barang ?? '' }}</td>
                        <td>{{ number_format($d->jumlah) }}</td>
                        <td>{{ number_format($d->barang_masuk) }}</td>
                        <td><strong>{{ number_format($d->sisa) }}</strong></td>
                        <td>
                            @php $pct = $d->jumlah > 0 ? round(($d->barang_masuk / $d->jumlah) * 100) : 0; @endphp
                            <div style="background:#e0e0e0;border-radius:999px;height:8px;width:120px;overflow:hidden;">
                                <div style="background:{{ $pct >= 100 ? '#28a745' : '#FFD700' }};height:100%;width:{{ min($pct, 100) }}%;border-radius:999px;transition:width .3s;"></div>
                            </div>
                            <span style="font-size:.72rem;color:#666;">{{ $pct }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
