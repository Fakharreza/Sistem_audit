<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pelaksanaan Audit: ') }} <span class="text-indigo-600 font-bold">{{ $audit->audit_code }}</span>
            </h2>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full border border-blue-200">
                Status: {{ strtoupper($audit->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 p-4 bg-white border-l-4 border-indigo-500 shadow-sm rounded-r-lg flex items-start">
                <svg class="w-6 h-6 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Panduan Penilaian Capability Level</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="font-bold text-red-600">0 (None):</span> 0-15% | 
                        <span class="font-bold text-yellow-600">0.5 (Partially):</span> 15-85% | 
                        <span class="font-bold text-green-600">1 (Fully):</span> 85-100%
                    </p>
                </div>
            </div>

            <div class="bg-white shadow-lg sm:rounded-xl overflow-hidden border border-gray-100">
                
                <div class="bg-slate-50 border-b border-gray-200 pt-2 px-2 overflow-x-auto">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500" id="domainTabs">
                        @foreach($audit->domains as $index => $domain)
                            <li class="mr-1">
                                <button type="button" 
                                        onclick="switchTab('tab-{{ $domain->id }}', this)" 
                                        class="tab-btn inline-block px-6 py-3 border-b-2 rounded-t-lg transition-all duration-200 {{ $index === 0 ? 'border-indigo-600 text-indigo-700 bg-white shadow-sm font-bold' : 'border-transparent hover:text-gray-700 hover:border-gray-300 hover:bg-gray-100' }}">
                                    {{ $domain->code }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <form action="{{ route('auditor.audit.store_kuesioner', $audit->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="p-6 bg-slate-50 min-h-[500px]">
                        @foreach($audit->domains as $index => $domain)
                            <div id="tab-{{ $domain->id }}" class="tab-content {{ $index === 0 ? 'block' : 'hidden' }}">
                                
                                <div class="mb-6">
                                    <h3 class="text-2xl font-extrabold text-gray-900">{{ $domain->code }} - {{ $domain->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Silakan evaluasi aktivitas pada domain ini berdasarkan Capability Level.</p>
                                </div>

                                @if($domain->questions->isEmpty())
                                    <div class="p-8 text-center bg-white rounded-lg border border-dashed border-gray-300">
                                        <p class="text-gray-500 font-medium">Belum ada aktivitas/pertanyaan yang didaftarkan untuk domain ini.</p>
                                    </div>
                                @else
                                    
                                    @php
                                        $groupedQuestions = $domain->questions->sortBy('capability_level')->groupBy('capability_level');
                                    @endphp

                                    @foreach($groupedQuestions as $level => $questions)
                                        
                                        <div class="bg-yellow-300 text-yellow-900 font-extrabold text-lg px-4 py-3 mt-8 mb-4 rounded shadow-sm border border-yellow-400 flex items-center">
                                            <svg class="w-6 h-6 mr-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                            CAPABILITY LEVEL {{ $level }}
                                        </div>

                                        <div class="space-y-6">
                                            @foreach($questions as $question)
                                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:border-indigo-300 transition duration-200">
                                                    
                                                    <div class="bg-indigo-50 border-b border-gray-200 p-5">
                                                        <h4 class="text-lg font-bold text-indigo-900">{{ $question->activity_code }}</h4>
                                                        <p class="text-base text-gray-800 mt-2 leading-relaxed">{{ $question->description }}</p>
                                                    </div>

                                                    <div class="p-5 grid grid-cols-1 md:grid-cols-12 gap-6">
                                                        
                                                        <div class="md:col-span-7 space-y-4">
                                                            <div>
                                                                <label class="block text-xs font-bold text-gray-700 mb-1">Skor Penilaian <span class="text-red-500">*</span></label>
                                                                <select name="answers[{{ $question->id }}][score]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm" required>
                                                                    <option value="" disabled selected>-- Pilih Skor --</option>
                                                                    <option value="0">0 - None (Belum Ada)</option>
                                                                    <option value="0.5">0.5 - Partially (Sebagian)</option>
                                                                    <option value="1">1 - Fully (Sepenuhnya)</option>
                                                                </select>
                                                            </div>

                                                            <div>
                                                                <label class="block text-xs font-bold text-gray-700 mb-1">Kondisi Saat Ini</label>
                                                                <textarea name="answers[{{ $question->id }}][notes]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm" placeholder="Jelaskan realita di lapangan..."></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="md:col-span-5 flex flex-col">
                                                            <label class="block text-xs font-bold text-gray-700 mb-1">Bukti Dukung (Evidence)</label>
                                                            <div class="flex-1 flex items-center justify-center w-full">
                                                                <label class="flex flex-col items-center justify-center w-full h-full min-h-[100px] border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                                                    <div class="flex flex-col items-center justify-center py-4">
                                                                        <svg class="w-6 h-6 mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                                        <p class="text-xs text-gray-500 font-semibold">Klik / Tarik File</p>
                                                                    </div>
                                                                    <input type="file" name="answers[{{ $question->id }}][evidence]" class="hidden" accept=".pdf,.jpg,.jpeg,.png" />
                                                                </label>
                                                            </div>
                                                            <p class="mt-1 text-[10px] text-gray-400 text-center">PDF/JPG (Maks 2MB)</p>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach

                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between rounded-b-xl">
                        <p class="text-xs text-gray-500 font-medium">⚠️ Pastikan Anda mengecek semua Tab Domain sebelum menyimpan.</p>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Kuesioner
                        </button>
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
                btn.classList.remove('border-indigo-600', 'text-indigo-700', 'bg-white', 'shadow-sm', 'font-bold');
                btn.classList.add('border-transparent', 'hover:text-gray-700', 'hover:border-gray-300', 'hover:bg-gray-100');
            });

            btnElement.classList.remove('border-transparent', 'hover:text-gray-700', 'hover:border-gray-300', 'hover:bg-gray-100');
            btnElement.classList.add('border-indigo-600', 'text-indigo-700', 'bg-white', 'shadow-sm', 'font-bold');
        }
    </script>
</x-app-layout>