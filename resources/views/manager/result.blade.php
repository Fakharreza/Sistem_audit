<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Hasil Rekomendasi Perbaikan: <span class="text-indigo-600">{{ $audit->audit_code }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8 p-6 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-xl shadow-lg text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-2xl font-black mb-1">🎉 Evaluasi Selesai!</h3>
                    <p class="text-indigo-100 text-sm">Keseluruhan nilai rata-rata tingkat kapabilitas (ITML Score) untuk audit ini.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="block text-xs font-bold text-indigo-200 uppercase tracking-widest mb-1">ITML Score</span>
                        <div class="text-4xl font-black bg-white text-indigo-700 px-6 py-2 rounded-lg shadow-inner">
                            {{ number_format($audit->itml_score ?? 0, 2) }} <span class="text-base text-indigo-300 font-bold ml-1">/ 5.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Peta Kapabilitas Spesifik</h3>
                    <p class="text-sm text-gray-500 mt-1">Visualisasi grafik laba-laba untuk hasil temuan di sesi audit <span class="font-bold text-indigo-600">{{ $audit->audit_code }}</span>.</p>
                    <div class="relative w-full max-w-2xl mx-auto h-[400px] mt-6">
                        <canvas id="detailSpiderChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Prioritas Eksekusi (SAW)</h3>
                    <p class="text-sm text-gray-500 mt-1">Urutan rekomendasi perbaikan berdasarkan kalkulasi matriks evaluasi dan kriteria SAW.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-bold text-xs border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-center w-24">Peringkat</th>
                                <th class="px-6 py-4">Domain & Aktivitas</th>
                                <th class="px-6 py-4 text-center">Skor SAW (V)</th>
                                <th class="px-6 py-4">Status Kondisi (Notes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $index => $row)
                                <tr class="border-b {{ $index === 0 ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 text-center">
                                        @if($index === 0)
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-400 text-yellow-900 font-black text-lg shadow-sm">1</span>
                                        @elseif($index === 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-gray-800 font-bold shadow-sm">2</span>
                                        @elseif($index === 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-300 text-orange-900 font-bold shadow-sm">3</span>
                                        @else
                                            <span class="font-bold text-gray-500">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-black text-indigo-700 text-base mb-1">
                                            {{ $row['gap']->question->activity_code }}
                                            @if($index === 0)
                                                <span class="ml-2 bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded border border-red-200 uppercase tracking-wider">Top Priority</span>
                                            @endif
                                        </div>
                                        <div class="text-gray-700 text-sm leading-relaxed">{{ $row['gap']->question->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-black text-xl text-gray-900">{{ number_format($row['final_score'], 3) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 italic">
                                        "{{ $row['gap']->notes ?: 'Tidak ada catatan auditor.' }}"
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 mb-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Tindak Lanjut / Area of Improvement (AoI)</h3>
                        <p class="text-sm text-gray-500 mt-1">Roadmap perbaikan berjenjang beserta log progress pelaksanaan.</p>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg pb-4">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-white uppercase bg-blue-700">
                                <tr>
                                    <th rowspan="2" class="px-4 py-3 border border-blue-600 text-center align-middle w-48">Control Objective</th>
                                    <th rowspan="2" class="px-2 py-3 border border-blue-600 text-center align-middle w-16">Maturity</th>
                                    <th colspan="4" class="px-4 py-2 border border-blue-600 text-center">Area of Improvement (AoI)</th>
                                </tr>
                                <tr>
                                    <th class="px-4 py-2 border border-blue-600 text-center w-1/4 bg-blue-600">Level 2</th>
                                    <th class="px-4 py-2 border border-blue-600 text-center w-1/4 bg-blue-600">Level 3</th>
                                    <th class="px-4 py-2 border border-blue-600 text-center w-1/4 bg-blue-600">Level 4</th>
                                    <th class="px-4 py-2 border border-blue-600 text-center w-1/4 bg-blue-600">Level 5</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roadmaps as $item)
                                    <tr class="bg-white hover:bg-slate-50 border-b">
                                        <td class="px-4 py-3 font-medium text-gray-900 border border-gray-200 bg-gray-50">
                                            {{ $item['domain'] }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-center border border-gray-200 text-blue-700 bg-blue-50">
                                            {{ $item['maturity'] }}
                                        </td>
                                        
                                        @for($level = 2; $level <= 5; $level++)
                                            <td class="px-4 py-3 border border-gray-200 align-top {{ $level == $item['target_level'] ? 'bg-yellow-50' : '' }}">
                                                @if($level == $item['target_level'])
                                                    <div class="h-full flex flex-col justify-between">
                                                        <div>
                                                            <span class="text-[10px] font-bold text-yellow-800 bg-yellow-200 px-2 py-1 rounded inline-block mb-2 shadow-sm border border-yellow-300">TARGET PERBAIKAN</span><br>
                                                            <span class="font-medium text-gray-800">{{ $item['recommendation'] }}</span>
                                                        </div>
                                                        
                                                        @php
                                                            $currentNote = $progressNotes[$item['domain']] ?? '';
                                                        @endphp
                                                        <div class="mt-4 pt-3 border-t border-yellow-300">
                                                            <button type="button" 
                                                                    data-domain="{{ $item['domain'] }}" 
                                                                    data-note="{{ $currentNote }}"
                                                                    onclick="openProgressModal(this)" 
                                                                    class="text-[11px] font-bold text-indigo-700 bg-white hover:bg-indigo-50 border-2 border-indigo-200 px-3 py-2 rounded-lg transition-colors flex items-center justify-center gap-1.5 w-full shadow-sm">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                {{ $currentNote ? 'Edit Progress' : 'Update Progress' }}
                                                            </button>
                                                            @if($currentNote)
                                                                <p class="mt-2 text-xs text-gray-700 bg-white/60 p-2 rounded border border-yellow-300 italic line-clamp-3">"{{ $currentNote }}"</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($level < $item['target_level'] || $item['target_level'] == null)
                                                    <div class="text-center text-emerald-500 font-bold mt-2">
                                                        <svg class="w-6 h-6 mx-auto mb-1 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        <span class="text-xs">Achieved</span>
                                                    </div>
                                                @else
                                                    <div class="text-center text-gray-300 mt-2">-</div>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end rounded-b-xl">
                    <a href="{{ route('manager.dashboard') }}" class="px-6 py-2.5 bg-slate-800 rounded-lg text-sm font-bold text-white shadow-sm hover:bg-slate-900 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="progressModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-full max-w-xl shadow-2xl rounded-xl bg-white">
            <div class="mt-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-black text-gray-900">Catatan Progress Perbaikan</h3>
                    <button onclick="closeProgressModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('manager.audit.progress.store', $audit->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="domain_name" id="modalDomainName">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Domain</label>
                        <input type="text" id="modalDomainDisplay" disabled class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5 font-bold">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Detail Progress (Tindakan Lanjutan)</label>
                        <textarea name="notes" id="modalNotes" rows="5" required class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-full p-2.5 shadow-sm" placeholder="Contoh: Telah disusun draft SOP untuk tata kelola data..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeProgressModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">Simpan Progress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Modal Logic
        function openProgressModal(buttonElement) {
            const domainName = buttonElement.getAttribute('data-domain');
            const note = buttonElement.getAttribute('data-note');
            
            document.getElementById('modalDomainName').value = domainName;
            document.getElementById('modalDomainDisplay').value = domainName;
            document.getElementById('modalNotes').value = note;
            
            document.getElementById('progressModal').classList.remove('hidden');
        }

        function closeProgressModal() {
            document.getElementById('progressModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('detailSpiderChart');
            const labels = [];
            const dataScores = [];
            
            @foreach($roadmaps as $item)
                labels.push("{{ explode(' - ', $item['domain'])[0] }}");
                dataScores.push({{ $item['maturity'] }});
            @endforeach

            if(labels.length > 0) {
                new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Maturity Level (audit terbaru)',
                            data: dataScores, 
                            fill: true,
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            borderColor: 'rgb(59, 130, 246)', 
                            pointBackgroundColor: 'rgb(59, 130, 246)', 
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgb(59, 130, 246)',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        scales: {
                            r: {
                                angleLines: { display: true, color: 'rgba(0, 0, 0, 0.1)' },
                                grid: { color: 'rgba(0, 0, 0, 0.1)' },
                                suggestedMin: 0, suggestedMax: 5,
                                ticks: { stepSize: 1, backdropColor: 'transparent', font: { weight: 'bold' } },
                                pointLabels: { font: { size: 12, weight: 'bold' }, color: '#4B5563' }
                            }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
</x-app-layout>