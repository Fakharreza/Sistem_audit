<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Evaluasi Celah (Gap) Audit: ') }} <span class="text-indigo-600 font-bold">{{ $audit->audit_code }}</span>
            </h2>
            <a href="{{ route('manager.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 underline">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 p-5 bg-white border-l-4 border-indigo-500 shadow-sm rounded-r-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Panduan Penilaian Manajer (Persiapan SAW)</h3>
                        <p class="text-sm text-gray-600 mt-1 mb-2">Di bawah ini adalah daftar aktivitas yang <strong>belum memenuhi target</strong> (Skor 0 atau 0.5). Berikan penilaian pada setiap kriteria menggunakan skala 1-5 untuk menentukan prioritas perbaikan.</p>
                        <ul class="text-xs text-gray-600 list-disc ml-5 grid grid-cols-1 md:grid-cols-2 gap-1">
                            <li><span class="font-bold">Skala 1:</span> Sangat Rendah / Sangat Buruk</li>
                            <li><span class="font-bold">Skala 2:</span> Rendah / Buruk</li>
                            <li><span class="font-bold">Skala 3:</span> Sedang / Cukup</li>
                            <li><span class="font-bold">Skala 4:</span> Tinggi / Baik</li>
                            <li><span class="font-bold">Skala 5:</span> Sangat Tinggi / Sangat Baik</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form action="{{ route('manager.audit.calculate', $audit->id) }}" method="POST" id="formEvaluate">
                @csrf
                
                @foreach($gaps as $index => $gap)
                    <div class="bg-white shadow-sm border border-gray-200 rounded-xl mb-6 overflow-hidden hover:border-indigo-300 transition duration-200">
                        
                        <div class="bg-indigo-50 border-b border-gray-200 p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-indigo-200 text-indigo-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Level {{ $gap->question->capability_level }}</span>
                                <span class="bg-white text-gray-700 border border-gray-300 text-[10px] font-bold px-2 py-0.5 rounded">{{ $gap->question->domain->code ?? 'DOMAIN' }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-indigo-900">{{ $gap->question->activity_code }}</h4>
                            <p class="text-sm text-gray-800 mt-1 leading-relaxed">{{ $gap->question->description }}</p>
                        </div>

                        <div class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-8">
                            
                            <div class="bg-slate-50 p-4 rounded-lg border border-gray-100 h-full">
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">Laporan Temuan Auditor</h5>
                                
                                <div class="mb-4 flex items-center">
                                    <span class="text-sm font-semibold text-gray-700 mr-3">Skor Capability:</span>
                                    @if($gap->score == 0)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded bg-red-100 text-red-700 border border-red-200">0 - None (Belum Ada)</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-700 border border-yellow-200">0.5 - Partially (Sebagian)</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="text-sm font-semibold text-gray-700 block mb-2">Kondisi Saat Ini di Lapangan:</span>
                                    <div class="bg-white p-3 rounded border border-gray-200 min-h-[60px]">
                                        <p class="text-sm text-gray-800 italic">
                                            "{{ $gap->notes ?: 'Auditor tidak meninggalkan catatan khusus untuk kondisi ini.' }}"
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h5 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Penilaian Kriteria SAW
                                </h5>
                                
                                <div class="space-y-4">
                                    @foreach($criteria as $criterion)
                                        <div>
                                            <div class="flex justify-between items-end mb-1.5">
                                                <label class="block text-sm font-bold text-gray-800">{{ $criterion->name }}</label>
                                                <span class="text-[10px] font-bold {{ $criterion->type == 'benefit' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} px-2 py-0.5 rounded border {{ $criterion->type == 'benefit' ? 'border-green-200' : 'border-orange-200' }}">
                                                    {{ strtoupper($criterion->type) }}
                                                </span>
                                            </div>
                                            <select name="evaluations[{{ $gap->id }}][{{ $criterion->id }}]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm transition duration-150" required>
                                                <option value="" disabled selected>-- Pilih Nilai Skala 1-5 --</option>
                                                <option value="1">1 - Sangat Rendah</option>
                                                <option value="2">2 - Rendah</option>
                                                <option value="3">3 - Sedang</option>
                                                <option value="4">4 - Tinggi</option>
                                                <option value="5">5 - Sangat Tinggi</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

                <div class="bg-white p-5 border border-gray-200 flex items-center justify-between rounded-xl shadow-lg mt-8 sticky bottom-6 z-10">
                    <p class="text-xs text-gray-500 font-medium">Pastikan semua kriteria pada semua aktivitas telah dinilai.</p>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Proses Perhitungan SAW
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>