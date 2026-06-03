<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2">
                <h2 class="text-2xl font-black text-gray-800">Dashboard Manajer</h2>
                <p class="text-sm text-gray-500 mt-1">Pantau peta kapabilitas dan evaluasi hasil audit COBIT 2019 secara real-time.</p>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        Peta Kapabilitas COBIT 2019 
                        <span class="text-indigo-600 text-sm ml-2 bg-indigo-50 px-2 py-1 rounded">
                            Audit Terbaru: {{ $latestCompletedAudit ? $latestCompletedAudit->audit_code : 'Belum Ada' }}
                        </span>
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Visualisasi rata-rata tingkat kapabilitas (Level 0-5) pada audit terakhir yang diselesaikan.</p>
                    
                    <div class="relative w-full max-w-3xl mx-auto h-[450px]">
                        <canvas id="cobitRadarChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Evaluasi Hasil Audit COBIT 2019</h3>
                            <p class="text-sm text-gray-500 mt-1">Daftar sesi audit yang telah diselesaikan dan siap untuk dihitung rekomendasi perbaikannya (SAW).</p>
                        </div>
                    </div>

                    <!-- KOTAK SEARCH & FILTER MANAJER (TANPA DRAFT) -->
                    <form method="GET" action="{{ route('manager.dashboard') }}" class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col md:flex-row gap-4">
                        
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Cari Audit / Auditor</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kode atau nama..." class="w-full pl-10 pr-4 py-2 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="w-full md:w-32">
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Tahun</label>
                            <select name="year" class="w-full py-2 px-3 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="">Semua</option>
                                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2 w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                            @if(request()->anyFilled(['search', 'year']))
                                <a href="{{ route('manager.dashboard') }}" class="px-3 py-2 bg-white hover:bg-gray-100 text-gray-600 text-sm font-bold rounded-lg transition-colors border border-gray-300 shadow-sm flex items-center justify-center" title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">Kode Audit</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Auditor</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Tgl Selesai</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Status & ITML</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($audits as $audit)
                                    <tr class="bg-white border-b hover:bg-slate-50 transition duration-150">
                                        <td class="px-6 py-4 font-bold text-indigo-600">{{ $audit->audit_code }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $audit->auditor_name }}</td>
                                        <td class="px-6 py-4">{{ $audit->updated_at->translatedFormat('d F Y') }}</td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center justify-center gap-1.5">
                                                @if($audit->can_see_result)
                                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        COMPLETED
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                                        BELUM DINILAI
                                                    </span>
                                                @endif
                                                
                                                <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-indigo-50 text-indigo-700 border border-indigo-200" title="Skor Rata-Rata ITML">
                                                    ⭐ ITML: {{ number_format($audit->itml_score, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            @if($audit->status === 'completed')
                                                @php
                                                    $hasGaps = \App\Models\AuditResponse::where('audit_id', $audit->id)->where('score', '<', 1)->exists();
                                                    $isEvaluated = \App\Models\GapEvaluation::whereHas('auditResponse', function($query) use ($audit) {
                                                        $query->where('audit_id', $audit->id);
                                                    })->exists();
                                                    
                                                    // Tombol Pintar: Bisa lihat hasil kalau SUDAH DIEVALUASI atau NGGAK ADA GAP (Sempurna)
                                                    $canSeeResult = !$hasGaps || $isEvaluated;
                                                @endphp

                                                @if($canSeeResult)
                                                    <a href="{{ route('manager.audit.result', $audit->id) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white transition-all duration-200 bg-emerald-500 rounded-md shadow-sm hover:bg-emerald-600">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Lihat Hasil & Progress
                                                    </a>
                                                @else
                                                    <a href="{{ route('manager.audit.evaluate', $audit->id) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white transition-all duration-200 bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        Hitung SAW & Rekomendasi
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400 italic font-medium">Menunggu Auditor</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <p class="font-medium text-lg">Belum Ada Data Audit</p>
                                                <p class="text-sm mt-1">Sesi audit yang telah diselesaikan oleh auditor akan muncul di sini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 pagination-theme">
                        {{ $audits->links() }}
                    </div>

                    <style>
                        /* Warna tombol halaman yang sedang aktif (Indigo) */
                        .pagination-theme nav[role="navigation"] [aria-current="page"] span {
                            background-color: #4f46e5 !important; 
                            color: white !important;
                            border-color: #4f46e5 !important;
                            font-weight: 900 !important;
                        }
                        
                        /* Warna tombol angka/panah biasa (Putih) */
                        .pagination-theme nav[role="navigation"] a {
                            background-color: white !important;
                            color: #6b7280 !important;
                            border-color: #e5e7eb !important;
                            font-weight: 600 !important;
                            transition: all 0.2s ease-in-out;
                        }
                        
                        /* Warna saat tombol di-hover/disentuh mouse (Biru Muda) */
                        .pagination-theme nav[role="navigation"] a:hover {
                            background-color: #eef2ff !important; 
                            color: #4f46e5 !important;
                            border-color: #c7d2fe !important;
                        }
                        
                        /* Warna tombol panah kalau udah mentok / disable (Abu-abu pudar) */
                        .pagination-theme nav[role="navigation"] span[aria-disabled="true"] span {
                            background-color: #f9fafb !important;
                            color: #d1d5db !important;
                            border-color: #f3f4f6 !important;
                        }
                    </style>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('cobitRadarChart');
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Maturity Level (Audit Terbaru)',
                        data: {!! json_encode($chartData) !!}, 
                        fill: true,
                        backgroundColor: 'rgba(79, 70, 229, 0.2)', 
                        borderColor: 'rgb(79, 70, 229)', 
                        pointBackgroundColor: 'rgb(79, 70, 229)', 
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(79, 70, 229)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { display: true, color: 'rgba(0, 0, 0, 0.1)' },
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            suggestedMin: 0, suggestedMax: 5,
                            ticks: { stepSize: 1, backdropColor: 'transparent', font: { weight: 'bold' } },
                            pointLabels: { font: { size: 13, weight: 'bold' }, color: '#4B5563' }
                        }
                    },
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        });
    </script>
</x-app-layout>