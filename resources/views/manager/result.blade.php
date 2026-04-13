<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Hasil Rekomendasi Perbaikan: <span class="text-indigo-600">{{ $audit->audit_code }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 mb-8 text-white shadow-lg">
                <h3 class="text-2xl font-black mb-2">🎉 Kalkulasi SAW Selesai!</h3>
                <p class="text-indigo-100">Berikut adalah daftar prioritas perbaikan berdasarkan analisis kombinasi gap lapangan dan evaluasi manajerial. Aktivitas di <strong>Peringkat 1</strong> wajib ditindaklanjuti terlebih dahulu.</p>
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
                        <p class="text-sm text-gray-500 mt-1">Roadmap perbaikan berjenjang. Domain harus memenuhi level bawah terlebih dahulu sebelum direkomendasikan naik ke level selanjutnya.</p>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-white uppercase bg-blue-700">
                                <tr>
                                    <th rowspan="2" class="px-4 py-3 border border-blue-600 text-center align-middle w-48">Control Objective</th>
                                    <th rowspan="2" class="px-2 py-3 border border-blue-600 text-center align-middle w-16">Maturity</th>
                                    <th colspan="4" class="px-4 py-2 border border-blue-600 text-center">Area of Improvement (AoI)</th>
                                    <th rowspan="2" class="px-4 py-3 border border-blue-600 text-center align-middle w-24">Tahun Pemenuhan</th>
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
                                                    <span class="text-xs font-semibold text-yellow-800 bg-yellow-200 px-2 py-1 rounded inline-block mb-2 shadow-sm">TARGET PERBAIKAN</span><br>
                                                    <span class="font-medium text-gray-800">{{ $item['recommendation'] }}</span>
                                                @elseif($level < $item['target_level'] || $item['target_level'] == null)
                                                    <div class="text-center text-emerald-500 font-bold mt-2">
                                                        <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Achieved
                                                    </div>
                                                @else
                                                    <div class="text-center text-gray-300 mt-2">-</div>
                                                @endif
                                            </td>
                                        @endfor

                                        <td class="px-4 py-3 text-center border border-gray-200 font-black text-gray-800 bg-gray-50">
                                            {{ date('Y') + 1 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end rounded-b-xl">
                    <a href="{{ route('manager.dashboard') }}" class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Selesai & Kembali ke Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>