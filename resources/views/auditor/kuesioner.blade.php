<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-800">
                        Pelaksanaan Audit: <span class="text-indigo-600">{{ $audit->audit_code }}</span>
                    </h2>
                    
                    <div class="flex items-center gap-3 mt-2">
                        @if($isReadOnly)
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 flex items-center shadow-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                MODE BACA (SELESAI)
                            </span>
                        @else
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 flex items-center shadow-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                MODE EDIT (DRAFT)
                            </span>
                        @endif
                        <p class="text-sm text-gray-500 font-medium hidden sm:block">| Evaluasi tingkat kapabilitas untuk setiap aktivitas.</p>
                    </div>
                </div>

                <a href="{{ route('auditor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>

            @if(!$isReadOnly)
            <div class="mb-6 p-4 bg-white border-l-4 border-indigo-500 shadow-sm rounded-r-lg flex items-start">
                <svg class="w-6 h-6 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Panduan Penilaian Capability Level</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-bold text-red-600">0 (None):</span> 0-15% | 
                        <span class="font-bold text-amber-500">0.5 (Partially):</span> 15-85% | 
                        <span class="font-bold text-emerald-600">1 (Fully):</span> 85-100%
                    </p>
                </div>
            </div>
            @endif

            <div class="bg-white shadow-lg sm:rounded-xl overflow-hidden border border-gray-100 relative">
                
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
                                        data-domain-id="{{ $domain->id }}"
                                        data-status-class="{{ $statusClass }}"
                                        onclick="switchTab('tab-{{ $domain->id }}', this)" 
                                        class="tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 {{ $isActive ? 'bg-white border-indigo-500 text-indigo-700 font-bold translate-y-[1px]' : $statusClass . ' font-medium opacity-80 hover:opacity-100' }}">
                                    {{ $domain->code }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form id="auditForm" action="{{ route('auditor.audit.store_kuesioner', $audit->id) }}" method="POST" enctype="multipart/form-data">
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
                                        
                                        @php
                                            $flatActivities = [];
                                            foreach($groupedData[$domain->id]['levels'] as $level => $activities) {
                                                foreach($activities as $activityCode => $questions) {
                                                    $flatActivities[] = [
                                                        'level' => $level,
                                                        'code' => $activityCode,
                                                        'questions' => $questions
                                                    ];
                                                }
                                            }
                                            $totalActivities = count($flatActivities);
                                        @endphp

                                        <div class="mb-5 flex justify-between items-center bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                                            <span class="text-sm font-bold text-gray-600">
                                                Aktivitas <span id="counter-{{ $domain->id }}" class="text-indigo-600">1</span> dari {{ $totalActivities }}
                                            </span>
                                            
                                            <div class="flex gap-1">
                                                @for($i = 0; $i < $totalActivities; $i++)
                                                    <div id="dot-{{ $domain->id }}-{{ $i }}" class="h-2 w-2 rounded-full {{ $i === 0 ? 'bg-indigo-600 w-4' : 'bg-gray-300' }} transition-all duration-300"></div>
                                                @endfor
                                            </div>
                                        </div>

                                        @foreach($flatActivities as $idx => $act)
                                            <div id="step-{{ $domain->id }}-{{ $idx }}" class="activity-step-{{ $domain->id }} {{ $idx === 0 ? 'block' : 'hidden' }} animate-[fadeIn_0.3s_ease-in-out]">
                                                
                                                <div class="bg-yellow-300 text-yellow-900 font-extrabold text-lg px-4 py-3 mt-4 mb-4 rounded shadow-sm border border-yellow-400 flex items-center">
                                                    <svg class="w-6 h-6 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                    CAPABILITY LEVEL {{ $act['level'] }}
                                                </div>

                                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden border-indigo-200">
                                                    <div class="bg-indigo-50 border-b border-indigo-100 p-4 flex items-center">
                                                        <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded mr-3">AKTIVITAS</span>
                                                        <h4 class="text-lg font-bold text-indigo-900">
                                                            {{ $act['code'] }} - {{ $act['questions']->first()->activity_name ?? 'Deskripsi Aktivitas' }}
                                                        </h4>
                                                    </div>

                                                    <div class="divide-y divide-gray-100">
                                                        @foreach($act['questions'] as $qIdx => $question)
                                                            @php $savedAnswer = $existingResponses[$question->id] ?? null; @endphp
                                                            <div class="p-6 hover:bg-slate-50 transition duration-150">
                                                                <div class="flex mb-5">
                                                                    <div class="font-black text-indigo-600 mr-3 mt-0.5 text-lg">{{ $qIdx + 1 }}.</div>
                                                                    <p class="text-base text-gray-800 leading-relaxed font-medium">
                                                                        {{ $question->description }}
                                                                    </p>
                                                                </div>

                                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pl-0 sm:pl-7">
                                                                    <div class="md:col-span-7 space-y-4">
                                                                        <div>
                                                                            <label class="block text-xs font-bold text-gray-700 mb-1">Skor Penilaian</label>
                                                                            <!-- ONCHANGE UNTUK REALTIME PROGRESS -->
                                                                            <select name="answers[{{ $question->id }}][score]" data-domain-id="{{ $domain->id }}" onchange="updateRealTimeProgress()" class="score-select bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm disabled:bg-gray-100 disabled:text-gray-500 transition-colors" {{ $isReadOnly ? 'disabled' : '' }}>
                                                                                <option value="" {{ !$savedAnswer ? 'selected' : '' }}>-- Belum Dinilai --</option>
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
                                                                        <p class="text-[11px] text-indigo-500 font-medium mb-2 italic">
                                                                            💡 Hint: {{ $question->evidence_hint }}
                                                                        </p>
                                                                        @if($isReadOnly)
                                                                            @if($savedAnswer && $savedAnswer->evidence_file)
                                                                                <div class="flex flex-col justify-center items-center p-4 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm h-full min-h-[110px]">
                                                                                    <a href="{{ asset('storage/' . $savedAnswer->evidence_file) }}" target="_blank" class="text-white font-bold px-4 py-2 bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition w-full text-center text-xs">Buka Dokumen</a>
                                                                                </div>
                                                                            @else
                                                                                <div class="flex-1 flex items-center justify-center w-full h-full min-h-[110px] border-2 border-gray-200 border-dashed rounded-lg bg-gray-50 text-gray-400 text-xs font-semibold">Tidak ada bukti dukung</div>
                                                                            @endif
                                                                        @else
                                                                            @if($savedAnswer && $savedAnswer->evidence_file)
                                                                                <div class="mb-2 flex items-center justify-between p-2 bg-emerald-50 border border-emerald-200 rounded text-xs shadow-sm">
                                                                                    <div class="flex items-center text-emerald-700 font-bold">✓ Tersimpan</div>
                                                                                    <a href="{{ asset('storage/' . $savedAnswer->evidence_file) }}" target="_blank" class="text-emerald-700 hover:text-emerald-900 underline font-bold px-2 py-1 bg-white rounded border border-emerald-200 hover:bg-emerald-100 transition">Lihat File</a>
                                                                                </div>
                                                                            @endif
                                                                            <div class="flex-1 flex items-center justify-center w-full">
                                                                                <label id="dropzone-{{ $question->id }}" class="flex flex-col items-center justify-center w-full h-full {{ ($savedAnswer && $savedAnswer->evidence_file) ? 'min-h-[70px]' : 'min-h-[110px]' }} border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 transition-colors">
                                                                                    <div class="flex flex-col items-center justify-center py-2 text-center" id="preview-{{ $question->id }}">
                                                                                        <svg class="w-5 h-5 mb-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                                                        <p class="text-[11px] text-gray-500 font-semibold">{{ ($savedAnswer && $savedAnswer->evidence_file) ? 'Klik / Tarik file baru' : 'Klik / Tarik File ke Sini' }}</p>
                                                                                    </div>
                                                                                    <input type="file" name="answers[{{ $question->id }}][evidence]" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFile(this, '{{ $question->id }}')" />
                                                                                </label>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="mt-6 flex justify-between items-center border-t border-gray-100 pt-5">
                                                    @if($idx > 0)
                                                        <button type="button" onclick="changeStep({{ $domain->id }}, {{ $idx - 1 }}, {{ $totalActivities }})" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold shadow-sm hover:bg-gray-50 flex items-center transition-colors">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                                            Aktivitas Sebelumnya
                                                        </button>
                                                    @else
                                                        <div></div> 
                                                    @endif

                                                    @if($idx < $totalActivities - 1)
                                                        <button type="button" onclick="changeStep({{ $domain->id }}, {{ $idx + 1 }}, {{ $totalActivities }})" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-bold shadow-sm hover:bg-indigo-700 flex items-center transition-colors">
                                                            Aktivitas Selanjutnya
                                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                        </button>
                                                    @else
                                                        <span class="text-sm text-emerald-600 font-bold flex items-center bg-emerald-50 px-4 py-2 rounded border border-emerald-200">
                                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Semua Aktivitas Selesai
                                                        </span>
                                                    @endif
                                                </div>

                                            </div>
                                        @endforeach

                                    @endif
                                </div>

                                <div class="bg-white px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between rounded-b-xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] mt-4 gap-4 sticky bottom-0 z-20">
                                    <p class="text-xs text-gray-500 font-medium hidden sm:block">
                                        {{ $isReadOnly ? 'Anda sedang melihat arsip kuesioner.' : 'Setiap perubahan skor secara otomatis mempengaruhi progress tab di atas.' }}
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                                        @if(!$isReadOnly)
                                            <!-- TOMBOL SIMPAN DRAFT -->
                                            <button type="submit" name="action" value="draft" formnovalidate class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:text-indigo-600 hover:border-indigo-400 transition-all duration-200">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                                Simpan Draft
                                            </button>
                                        @endif

                                        @if($index < count($audit->domains) - 1)
                                            <button type="button" onclick="document.getElementById('tab-btn-{{ $audit->domains[$index+1]->id }}').click()" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg shadow-sm hover:bg-indigo-100 hover:border-indigo-300 transition-all duration-200">
                                                Pindah ke {{ $audit->domains[$index+1]->code }}
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </button>
                                        @else
                                            @if(!$isReadOnly)
                                                <!-- TOMBOL SELESAI (Picu Modal) -->
                                                <button type="button" onclick="triggerSubmitConfirmation(this)" class="inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

    <!-- MODAL POP-UP ERROR (PERTANYAAN BELUM LENGKAP) -->
    <div id="modal-error" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-red-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Belum Lengkap!</h3>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Terdapat <span id="unanswered-count" class="font-black text-red-600 text-lg mx-1 px-2 py-0.5 bg-red-50 rounded">0</span> pertanyaan yang belum Anda nilai. Silakan lengkapi semua penilaian.
                </p>
                <button type="button" onclick="closeModal('modal-error')" class="w-full inline-flex justify-center items-center px-5 py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                    Mengerti, Saya Lengkapi
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP KONFIRMASI (YAKIN SELESAI?) -->
    <div id="modal-confirm" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center border-b border-gray-50">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-indigo-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Selesaikan Audit?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Jika Anda menekan <strong class="text-indigo-600 font-bold">Ya, Selesaikan</strong>, status audit akan dikunci secara permanen dan dikirim ke Manajer.
                </p>
            </div>
            <div class="bg-gray-50 p-6 flex flex-col gap-3 rounded-b-2xl">
                <button type="button" onclick="executeSubmit()" class="w-full px-5 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex justify-center items-center">
                    Ya, Selesaikan Kuesioner
                </button>
                <button type="button" onclick="closeModal('modal-confirm')" class="w-full px-5 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all">
                    Batal, Kembali Edit
                </button>
            </div>
        </div>
    </div>

    <!-- SIHIR ANTI-PURGE TAILWIND (Biar warna tab muncul 100%) -->
    <div class="hidden bg-gray-100 text-gray-500 border-gray-200 bg-yellow-100 text-yellow-700 border-yellow-300 bg-blue-100 text-blue-700 border-blue-300 opacity-80 hover:opacity-100"></div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-\[fadeIn_0\.3s_ease-in-out\] {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>

    <script>
        // INISIALISASI REAL-TIME PROGRESS SAAT HALAMAN DIMUAT
        document.addEventListener('DOMContentLoaded', function() {
            updateRealTimeProgress();
        });

        // FUNGSI GANTI TAB
        function switchTab(tabId, btnElement) {
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('block');
            });
            
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('block');

            // Reset Tab Lain
            document.querySelectorAll('.tab-btn').forEach(btn => {
                const currentStatus = btn.getAttribute('data-status-class');
                btn.className = `tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 font-medium opacity-80 hover:opacity-100 ${currentStatus}`;
            });

            // Set Tab Aktif
            btnElement.className = 'tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 bg-white border-indigo-500 text-indigo-700 font-bold translate-y-[1px]';
            
            document.getElementById('domainTabs').scrollIntoView({ behavior: 'smooth' });
        }

        // FUNGSI NAVIGASI AKTIVITAS
        function changeStep(domainId, targetIndex, totalSteps) {
            document.querySelectorAll(`.activity-step-${domainId}`).forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('block');
            });
            
            const targetElement = document.getElementById(`step-${domainId}-${targetIndex}`);
            if(targetElement) {
                targetElement.classList.remove('hidden');
                targetElement.classList.add('block');
            }

            document.getElementById(`counter-${domainId}`).innerText = targetIndex + 1;

            for(let i=0; i<totalSteps; i++) {
                const dot = document.getElementById(`dot-${domainId}-${i}`);
                if(i === targetIndex) {
                    dot.className = "h-2 rounded-full bg-indigo-600 w-4 transition-all duration-300";
                } else {
                    dot.className = "h-2 w-2 rounded-full bg-gray-300 transition-all duration-300";
                }
            }
            document.getElementById('domainTabs').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // FUNGSI PREVIEW FILE
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

        // FUNGSI REAL-TIME PROGRESS UPDATE WARNA TAB
        function updateRealTimeProgress() {
            const tabs = document.querySelectorAll('.tab-btn');
            
            tabs.forEach(tab => {
                const domainId = tab.getAttribute('data-domain-id');
                if(!domainId) return;

                const selects = document.querySelectorAll(`.score-select[data-domain-id="${domainId}"]`);
                if(selects.length === 0) return;

                let answered = 0;
                selects.forEach(sel => {
                    if(sel.value !== "") answered++;
                });

                let statusClass = '';
                if(answered === 0) {
                    statusClass = 'bg-gray-100 text-gray-500 border-gray-200'; // Kosong
                } else if(answered < selects.length) {
                    statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-300'; // Sebagian
                } else {
                    statusClass = 'bg-blue-100 text-blue-700 border-blue-300'; // Penuh
                }

                // Simpan kelas baru ke data attribute
                tab.setAttribute('data-status-class', statusClass);

                // Update warna tab jika tab tersebut BUKAN yang sedang aktif (diklik)
                if(!tab.classList.contains('border-indigo-500')) {
                    tab.className = `tab-btn inline-block px-6 py-3 rounded-t-lg transition-all duration-200 border-t-2 border-l-2 border-r-2 font-medium opacity-80 hover:opacity-100 ${statusClass}`;
                }
            });
        }

        // FUNGSI MODAL DAN VALIDASI SUBMIT
        let pendingSubmitBtn = null;

        function triggerSubmitConfirmation(buttonElement) {
            let unanswered = 0;
            const allSelects = document.querySelectorAll('.score-select');
            
            allSelects.forEach(select => {
                if (select.value === "") {
                    unanswered++;
                }
            });

            if (unanswered > 0) {
                // Tampilkan Modal Error Cantik
                document.getElementById('unanswered-count').innerText = unanswered;
                document.getElementById('modal-error').classList.remove('hidden');
                return false;
            }

            // Tampilkan Modal Konfirmasi Cantik
            pendingSubmitBtn = buttonElement;
            document.getElementById('modal-confirm').classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function executeSubmit() {
            closeModal('modal-confirm');
            
            if(pendingSubmitBtn) {
                pendingSubmitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
                pendingSubmitBtn.disabled = true;
                pendingSubmitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }

            const form = document.getElementById('auditForm');
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'submit';
            form.appendChild(actionInput);
            
            form.submit();
        }
    </script>
</x-app-layout>