<x-app-layout>
    <x-slot name="title">Supplier</x-slot>

    <div class="card" style="max-width:600px;margin-bottom:1.5rem;">
        <div class="card-header"><h3>Tambah Supplier</h3></div>
        <form method="POST" action="{{ route('supplier.store') }}" style="display:flex;gap:.5rem;">
            @csrf
            <input type="text" name="nama_supplier" class="form-control" placeholder="Nama supplier..." required>
            <button class="btn btn-primary">Tambah</button>
        </form>
    </div>

    <div class="card">
        <div class="table-container">
            <table>
                <thead><tr><th>Nama Supplier</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('supplier.update', $s) }}" style="display:flex;gap:.5rem;">
                                @csrf @method('PUT')
                                <input type="text" name="nama_supplier" class="form-control" value="{{ $s->nama_supplier }}" style="max-width:300px;">
                                <button class="btn btn-warning btn-sm">Update</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('supplier.destroy', $s) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2"><div class="empty-state"><p>Belum ada data supplier.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $suppliers->links() }}</div>
    </div>
</x-app-layout>
