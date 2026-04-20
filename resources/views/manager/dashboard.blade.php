<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Manajer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                                            @if($audit->status === 'completed')
                                                <div class="flex flex-col items-center justify-center gap-1.5">
                                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        COMPLETED
                                                    </span>
                                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-indigo-50 text-indigo-700 border border-indigo-200" title="Skor ITML">
                                                        ⭐ ITML: {{ number_format($audit->itml_score, 2) }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">DRAFT</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            @if($audit->status === 'completed')
                                                @php
                                                    $isEvaluated = \App\Models\GapEvaluation::whereHas('auditResponse', function($query) use ($audit) {
                                                        $query->where('audit_id', $audit->id);
                                                    })->exists();
                                                @endphp

                                                @if($isEvaluated)
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
                                                <span class="text-xs text-gray-400 italic">Menunggu Auditor</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <p class="font-medium text-lg">Belum Ada Data Audit</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

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
                        label: 'Rata-rata Kapabilitas (Audit Terbaru)',
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