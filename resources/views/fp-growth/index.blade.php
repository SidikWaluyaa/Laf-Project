<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>

    <div class="page-header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Analisis Rekomendasi Paket Promo</h2>
            <p class="text-sm text-gray-500">Menggunakan Algoritma FP-Growth untuk menemukan pola pembelian konsumen.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Panel Parameter -->
        <div class="md:col-span-1">
            <div class="card">
                <div class="card-header">
                    <h3>Parameter Analisis</h3>
                </div>
                <form action="{{ route('fp-growth.process') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="min_support">Minimum Support (%)</label>
                        <input type="number" name="min_support" id="min_support" class="form-control" value="10" step="0.1" min="0.1" max="100" required>
                        <small class="text-gray-400">Persentase kemunculan produk dalam seluruh transaksi.</small>
                    </div>
                    <div class="form-group">
                        <label for="min_confidence">Minimum Confidence (%)</label>
                        <input type="number" name="min_confidence" id="min_confidence" class="form-control" value="50" step="0.1" min="0.1" max="100" required>
                        <small class="text-gray-400">Kuatnya hubungan antar produk dalam satu paket.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-full justify-center mt-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Mulai Analisis
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Hasil (Placeholder) -->
        <div class="md:col-span-2">
            <div class="card h-full">
                <div class="card-header">
                    <h3>Hasil Aturan Asosiasi (Association Rules)</h3>
                </div>
                
                @if(count($results) === 0)
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="empty-state-title">Belum Ada Data Analisis</div>
                        <p>Silakan tentukan parameter di samping dan klik "Mulai Analisis" untuk melihat rekomendasi paket promo berdasarkan data transaksi riil.</p>
                    </div>
                @else
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jika Membeli (Antecedent)</th>
                                    <th>Maka Juga Membeli (Consequent)</th>
                                    <th>Support (%)</th>
                                    <th>Confidence (%)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $res)
                                <tr>
                                    <td><span class="badge badge-info">{{ $res['ante'] }}</span></td>
                                    <td><span class="badge badge-success">{{ $res['cons'] }}</span></td>
                                    <td>{{ $res['support'] }}%</td>
                                    <td>{{ $res['confidence'] }}%</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button type="button" class="btn btn-outline btn-sm" onclick="toggleDetail('detail-{{ $index }}')">
                                                Detail
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="detail-{{ $index }}" style="display: none; background: #fdfdfd;">
                                    <td colspan="5" class="p-4 border-l-4 border-blue-400">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Interpretasi Kalimat:</h5>
                                                <p class="text-sm text-gray-700">
                                                    Dari total <strong>{{ $res['total_transactions'] }}</strong> transaksi, terdapat <strong>{{ $res['count_both'] }}</strong> transaksi di mana pelanggan membeli <strong>{{ $res['ante'] }}</strong> dan <strong>{{ $res['cons'] }}</strong> secara bersamaan. 
                                                    Tingkat keyakinan (Confidence) sebesar <strong>{{ $res['confidence'] }}%</strong> menunjukkan bahwa pelanggan yang membeli {{ $res['ante'] }} memiliki peluang sangat besar untuk juga membeli {{ $res['cons'] }}.
                                                </p>
                                            </div>
                                            <div>
                                                <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Simulasi Perhitungan (Matematis):</h5>
                                                <div class="text-xs font-mono bg-white p-3 rounded border border-blue-100">
                                                    <div class="mb-2">
                                                        <strong>Support:</strong> (Kemunculan Bersama / Total Transaksi)<br>
                                                        = {{ $res['count_both'] }} / {{ $res['total_transactions'] }}<br>
                                                        = <strong>{{ $res['support'] }}%</strong>
                                                    </div>
                                                    <div>
                                                        <strong>Confidence:</strong> (Kemunculan Bersama / Kemunculan {{ $res['ante'] }})<br>
                                                        = {{ $res['count_both'] }} / {{ $res['count_ante'] }}<br>
                                                        = <strong>{{ $res['confidence'] }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                        <h4 class="text-sm font-bold text-yellow-800 mb-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Interpretasi Hasil:
                        </h4>
                        <p class="text-xs text-yellow-700">
                            Sistem mendeteksi pola kuat antara produk di atas dalam periode yang Anda pilih. 
                            Gunakan data ini untuk membuat paket bundling atau menempatkan produk tersebut dalam satu area display.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .bg-yellow-50 { background-color: #fffbeb; }
        .border-yellow-100 { border-color: #fef3c7; }
        .text-yellow-800 { color: #92400e; }
        .text-yellow-700 { color: #b45309; }
    </style>
    @endpush
    @push('scripts')
    <script>
        function toggleDetail(id) {
            const el = document.getElementById(id);
            if (el.style.display === 'none') {
                el.style.display = 'table-row';
            } else {
                el.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>
