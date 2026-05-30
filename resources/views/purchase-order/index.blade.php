<x-app-layout>
    <x-slot name="title">Purchase Order</x-slot>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <form method="GET" class="search-bar" style="margin-bottom:0;">
            <input type="text" name="search" class="form-control" placeholder="Cari supplier..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm" type="submit">Cari</button>
        </form>
        <a href="{{ route('purchase-order.create') }}" class="btn btn-primary">+ PO Baru</a>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Tanggal</th><th>Nomor PO</th><th>Supplier</th><th>Status</th><th>Items</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr>
                        <td>{{ $po->tanggal ? $po->tanggal->format('d/m/Y') : '-' }}</td>
                        <td><strong>{{ $po->nomor_po ?? '-' }}</strong></td>
                        <td>{{ $po->supplier->nama_supplier ?? '-' }}</td>
                        <td>
                            @php $statusClass = match($po->status) { 'selesai' => 'badge-success', 'sebagian' => 'badge-warning', 'dikirim' => 'badge-info', default => 'badge-secondary' }; @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($po->status) }}</span>
                        </td>
                        <td>{{ $po->detail->count() }} item</td>
                        <td style="display:flex;gap:.5rem;">
                            <a href="{{ route('purchase-order.show', $po) }}" class="btn btn-outline btn-sm">Detail</a>
                            <a href="{{ route('purchase-order.edit', $po) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" action="{{ route('purchase-order.destroy', $po) }}" onsubmit="return confirm('Hapus PO ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:#666;">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $purchaseOrders->links() }}</div>
    </div>
</x-app-layout>
