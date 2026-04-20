<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pelaksanaan Audit: ') }} <span class="text-indigo-600 font-bold">{{ $audit->audit_code }}</span>
            </h2>
            <div class="flex gap-2">
                @if($isReadOnly)
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        MODE BACA (SELESAI)
                    </span>
                @else
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full border border-blue-200 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        MODE EDIT (DRAFT)
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(!$isReadOnly)
            <div class="mb-6 p-4 bg-white border-l-4 border-indigo-500 shadow-sm rounded-r-lg flex items-start">
                <svg class="w-6 h-6 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Panduan Penilaian Capability Level</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-bold text-red-600">0 (None):</span> 0-15% | 
                        <span class="font-bold text-yellow-600">0.5 (Partially):</span> 15-85% | 
                        <span class="font-bold text-green-600">1 (Fully):</span> 85-100%
                    </p>
                </div>
            </div>
            @endif

            <div class="bg-white shadow-lg sm:rounded-xl overflow-hidden border border-gray-100">
                
                <div class="bg-slate-50 pt-3 px-3 overflow-x-auto border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="domainTabs">
                        @foreach($audit->domains as $index => $domain)
                            @php
                                $statusClass = $tabStatuses[$domain->id];
                                $isActive = $index === 0;
                            @endphp
                            <li class="mr-1.5">
                                <button type="button" 
                                        id="tab-btn-{{ $domain->id }}"
                                        data-status-class="{{ $statusClass }}"
                                        onclick="switchTab('tab-{{ $domain->id }}', this)" 
                                        class="tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 {{ $isActive ? 'bg-white border-indigo-500 text-indigo-700 font-bold translate-y-[1px]' : $statusClass . ' font-medium opacity-80 hover:opacity-100' }}">
                                    {{ $domain->code }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form action="{{ route('auditor.audit.store_kuesioner', $audit->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="bg-white min-h-[500px]">
                        @foreach($audit->domains as $index => $domain)
                            <div id="tab-{{ $domain->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                                
                                <div class="p-6 bg-slate-50 border-b border-gray-200">
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $domain->code }} - {{ $domain->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $isReadOnly ? 'Riwayat penilaian aktivitas pada domain ini.' : 'Silakan evaluasi aktivitas pada domain ini berdasarkan tingkat Capability Level.' }}
                                    </p>
                                </div>

                                <div class="p-6">
                                    @if(!isset($groupedData[$domain->id]) || $groupedData[$domain->id]['levels']->isEmpty())
                                        <div class="p-8 text-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                            <p class="text-gray-500 font-medium">Belum ada aktivitas/pertanyaan yang didaftarkan untuk domain ini.</p>
                                        </div>
                                    @else
                                        
                                        @foreach($groupedData[$domain->id]['levels'] as $level => $activities)
                                            <div class="bg-yellow-300 text-yellow-900 font-extrabold text-lg px-4 py-3 mt-4 mb-4 rounded shadow-sm border border-yellow-400 flex items-center">
                                                <svg class="w-6 h-6 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                CAPABILITY LEVEL {{ $level }}
                                            </div>

                                            <div class="space-y-6">
                                                @foreach($activities as $activityCode => $questions)
                                                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:border-indigo-300 transition duration-200">
                                                        
                                                        <div class="bg-indigo-50 border-b border-gray-200 p-4 flex items-center">
                                                            <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded mr-3">AKTIVITAS</span>
                                                            <h4 class="text-lg font-bold text-indigo-900">
                                                                {{ $activityCode }} - {{ $questions->first()->activity_name ?? 'Deskripsi Aktivitas' }}
                                                            </h4>
                                                        </div>

                                                        <div class="divide-y divide-gray-100">
                                                            @foreach($questions as $idx => $question)
                                                                @php
                                                                    $savedAnswer = $existingResponses[$question->id] ?? null;
                                                                @endphp

                                                                <div class="p-6 hover:bg-slate-50 transition duration-150">
                                                                    <div class="flex mb-5">
                                                                        <div class="font-black text-indigo-600 mr-3 mt-0.5 text-lg">{{ $idx + 1 }}.</div>
                                                                        <p class="text-base text-gray-800 leading-relaxed font-medium">
                                                                            {{ $question->description }}
                                                                        </p>
                                                                    </div>

                                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pl-0 sm:pl-7">
                                                                        
                                                                        <div class="md:col-span-7 space-y-4">
                                                                            <div>
                                                                                <label class="block text-xs font-bold text-gray-700 mb-1">Skor Penilaian</label>
                                                                                <select name="answers[{{ $question->id }}][score]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm disabled:bg-gray-100 disabled:text-gray-500" {{ $isReadOnly ? 'disabled' : 'required' }}>
                                                                                    <option value="" disabled {{ !$savedAnswer ? 'selected' : '' }}>-- Belum Dinilai --</option>
                                                                                    <option value="0" {{ ($savedAnswer && $savedAnswer->score == '0') ? 'selected' : '' }}>0 - None (Belum Ada)</option>
                                                                                    <option value="0.5" {{ ($savedAnswer && $savedAnswer->score == '0.5') ? 'selected' : '' }}>0.5 - Partially (Sebagian)</option>
                                                                                    <option value="1" {{ ($savedAnswer && $savedAnswer->score == '1') ? 'selected' : '' }}>1 - Fully (Sepenuhnya)</option>
                                                                                </select>
                                                                            </div>

                                                                            <div>
                                                                                <label class="block text-xs font-bold text-gray-700 mb-1">Kondisi Saat Ini</label>
                                                                                <textarea name="answers[{{ $question->id }}][notes]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm disabled:bg-gray-100 disabled:text-gray-500" placeholder="{{ $isReadOnly ? 'Tidak ada catatan.' : 'Jelaskan realita di lapangan...' }}" {{ $isReadOnly ? 'disabled' : '' }}>{{ $savedAnswer->notes ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="md:col-span-5 flex flex-col">
                                                                            <label class="block text-xs font-bold text-gray-700 mb-1">Bukti Dukung (Evidence)</label>
                                                                            
                                                                            @if($isReadOnly)
                                                                                @if($savedAnswer && $savedAnswer->evidence_file)
                                                                                    <div class="flex flex-col justify-center items-center p-4 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm h-full min-h-[110px]">
                                                                                        <div class="flex items-center text-indigo-700 font-bold mb-2">
                                                                                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                                            File Tersimpan
                                                                                        </div>
                                                                                        <a href="{{ asset('storage/' . $savedAnswer->evidence_file) }}" target="_blank" class="text-white font-bold px-4 py-2 bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition w-full text-center text-xs">Buka Dokumen</a>
                                                                                    </div>
                                                                                @else
                                                                                    <div class="flex-1 flex items-center justify-center w-full h-full min-h-[110px] border-2 border-gray-200 border-dashed rounded-lg bg-gray-50 text-gray-400 text-xs font-semibold">
                                                                                        Tidak ada bukti dukung
                                                                                    </div>
                                                                                @endif

                                                                            @else
                                                                                @if($savedAnswer && $savedAnswer->evidence_file)
                                                                                    <div class="mb-2 flex items-center justify-between p-2 bg-emerald-50 border border-emerald-200 rounded text-xs shadow-sm">
                                                                                        <div class="flex items-center text-emerald-700 font-bold">
                                                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                                            Tersimpan
                                                                                        </div>
                                                                                        <a href="{{ asset('storage/' . $savedAnswer->evidence_file) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 underline font-bold px-2 py-1 bg-white rounded border border-emerald-200 hover:bg-emerald-100 transition">Lihat File</a>
                                                                                    </div>
                                                                                @endif

                                                                                <div class="flex-1 flex items-center justify-center w-full">
                                                                                    <label id="dropzone-{{ $question->id }}" class="flex flex-col items-center justify-center w-full h-full {{ ($savedAnswer && $savedAnswer->evidence_file) ? 'min-h-[70px]' : 'min-h-[110px]' }} border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 transition-colors">
                                                                                        <div class="flex flex-col items-center justify-center py-2 text-center" id="preview-{{ $question->id }}">
                                                                                            <svg class="w-5 h-5 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                                                            <p class="text-[11px] text-gray-500 font-semibold">{{ ($savedAnswer && $savedAnswer->evidence_file) ? 'Klik / Tarik file baru untuk mengganti' : 'Klik / Tarik File ke Sini' }}</p>
                                                                                        </div>
                                                                                        <input type="file" name="answers[{{ $question->id }}][evidence]" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, '{{ $question->id }}')" />
                                                                                    </label>
                                                                                </div>
                                                                                <p class="mt-1 text-[10px] text-gray-400 text-center">Format PDF/JPG (Maks 2MB)</p>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach

                                    @endif
                                </div>

                                <div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between rounded-b-xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] mt-4 gap-4 sticky bottom-0 z-20">
                                    <p class="text-xs text-gray-500 font-medium hidden sm:block">
                                        {{ $isReadOnly ? 'Anda sedang melihat arsip kuesioner.' : 'Perubahan belum tersimpan sampai Anda menekan Simpan Draft.' }}
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                                        
                                        @if(!$isReadOnly)
                                            <button type="submit" name="action" value="draft" formnovalidate class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-400 transition-all duration-200">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                                Simpan Draft
                                            </button>
                                        @endif

                                        @if($index < count($audit->domains) - 1)
                                            <button type="button" onclick="document.getElementById('tab-btn-{{ $audit->domains[$index+1]->id }}').click()" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm hover:bg-indigo-100 hover:border-indigo-300 transition-all duration-200">
                                                Lanjut ke {{ $audit->domains[$index+1]->code }}
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </button>
                                        @else
                                            @if(!$isReadOnly)
                                                <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Selesaikan Kuesioner
                                                </button>
                                            @else
                                                <a href="{{ route('auditor.dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white bg-slate-800 rounded-lg shadow-md hover:bg-slate-900 transition-all duration-200">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                                    Kembali ke Dashboard
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId, btnElement) {
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('block');
            });
            
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('block');

            document.querySelectorAll('.tab-btn').forEach(btn => {
                const originalColorClass = btn.getAttribute('data-status-class');
                btn.className = `tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 font-medium opacity-80 hover:opacity-100 ${originalColorClass}`;
            });

            btnElement.className = 'tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 bg-white border-indigo-500 text-indigo-700 font-bold translate-y-[1px]';
            
            document.getElementById('domainTabs').scrollIntoView({ behavior: 'smooth' });
        }

        function previewFile(input, questionId) {
            const previewContainer = document.getElementById('preview-' + questionId);
            const dropzone = document.getElementById('dropzone-' + questionId);
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                
                previewContainer.innerHTML = `
                    <svg class="w-8 h-8 mb-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-xs text-indigo-700 font-bold truncate max-w-[180px] text-center" title="${fileName}">${fileName}</p>
                    <p class="text-[10px] text-indigo-400 mt-0.5">Siap disimpan (Draft)</p>
                `;
                
                dropzone.classList.remove('border-gray-300', 'bg-gray-50');
                dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
            }
        }
    </script>
</x-app-layout>