<x-app-layout>
    <x-slot name="title">Preview Import Produk</x-slot>

    <div class="page-header" style="margin-bottom:1.5rem;">
        <h3 style="margin:0;font-weight:700;font-size:1.25rem;">Preview Import Data</h3>
        <p style="margin:0;color:var(--text-muted);font-size:.9rem;">Periksa kembali data sebelum disimpan ke database</p>
    </div>

    @if(count($errors) > 0)
    <div class="alert alert-danger" style="margin-bottom:1.5rem;padding:1rem;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;">
        <h4 style="margin:0 0 .5rem;color:#991b1b;font-weight:600;"><svg style="width:1.2rem;height:1.2rem;vertical-align:middle;margin-top:-2px;margin-right:.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg> {{ count($errors) }} Baris Data Error Dilewati</h4>
        <ul style="margin:0;padding-left:1.5rem;color:#b91c1c;font-size:.85rem;">
            @foreach(array_slice($errors, 0, 5) as $error)
            <li>Baris {{ $error['row'] }} ({{ $error['data'] }}): {{ implode(', ', $error['messages']) }}</li>
            @endforeach
            @if(count($errors) > 5)
            <li><i>...dan {{ count($errors) - 5 }} error lainnya</i></li>
            @endif
        </ul>
    </div>
    @endif

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;">Data Valid ({{ count($validData) }} Produk)</h3>
            
            <div style="display:flex;gap:.5rem;">
                <a href="{{ route('produk.index') }}" class="btn btn-outline btn-sm">Batal</a>
                <form method="POST" action="{{ route('produk.import.confirm') }}" style="margin:0;">
                    @csrf
                    <input type="hidden" name="data" value="{{ json_encode($validData) }}">
                    <button type="submit" class="btn btn-primary btn-sm" {{ count($validData) === 0 ? 'disabled' : '' }}>✓ Konfirmasi & Simpan</button>
                </form>
            </div>
        </div>
        <div class="table-container" style="max-height:500px;overflow-y:auto;">
            <table>
                <thead>
                    <tr><th>Kategori</th><th>Nama Barang</th><th>Variasi</th><th>Satuan</th><th>HPP</th></tr>
                </thead>
                <tbody>
                    @forelse($validData as $d)
                    <tr>
                        <td><span class="badge badge-info">{{ $d['kategori_nama'] }}</span></td>
                        <td><strong>{{ $d['nama_barang'] }}</strong></td>
                        <td>{{ $d['variasi_barang'] ?? '-' }}</td>
                        <td>{{ $d['satuan'] }}</td>
                        <td>Rp {{ number_format($d['hpp'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">
                            Tidak ada data valid yang dapat disimpan. Silakan perbaiki file Excel Anda dan coba lagi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
