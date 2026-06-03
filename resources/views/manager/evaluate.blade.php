<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-800">
                        Evaluasi Celah (Gap) Audit: <span class="text-indigo-600">{{ $audit->audit_code }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Berikan penilaian bobot kriteria untuk menentukan prioritas rekomendasi (Metode SAW).</p>
                </div>

                <button type="button" onclick="openBackModal()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200 w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </button>
            </div>
            
            <div class="mb-6 p-5 bg-white border-l-4 border-indigo-500 shadow-sm rounded-r-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Panduan Penilaian Manajer (Persiapan SAW)</h3>
                        <p class="text-sm text-gray-600 mt-1 mb-2">Di bawah ini adalah daftar aktivitas yang <strong>belum memenuhi target</strong>. Berikan penilaian menggunakan skala 1-5 untuk menentukan prioritas perbaikan.</p>
                        <ul class="text-xs text-gray-600 list-disc ml-5 grid grid-cols-1 md:grid-cols-2 gap-1">
                            <li><span class="font-bold">Skala 1:</span> Sangat Rendah / Sangat Buruk</li>
                            <li><span class="font-bold">Skala 2:</span> Rendah / Buruk</li>
                            <li><span class="font-bold">Skala 3:</span> Sedang / Cukup</li>
                            <li><span class="font-bold">Skala 4:</span> Tinggi / Baik</li>
                            <li><span class="font-bold">Skala 5:</span> Sangat Tinggi / Sangat Baik</li>
                        </ul>
                    </div>
                </div>
                
                <div id="save-indicator" class="hidden items-center text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full shadow-sm whitespace-nowrap transition-all duration-300">
                    <svg class="w-4 h-4 mr-1 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Draft Tersimpan!
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
                                        <span class="px-2.5 py-1 text-xs font-bold rounded bg-amber-100 text-amber-700 border border-amber-200">0.5 - Partially (Sebagian)</span>
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
                                                <span class="text-[10px] font-bold {{ $criterion->type == 'benefit' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-orange-100 text-orange-700 border-orange-200' }} px-2 py-0.5 rounded border">
                                                    {{ strtoupper($criterion->type) }}
                                                </span>
                                            </div>
                                            <select name="evaluations[{{ $gap->id }}][{{ $criterion->id }}]" class="eval-select bg-white border border-gray-300 text-gray-900 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 shadow-sm transition duration-150" required>
                                                <option value="" disabled selected>-- Pilih Nilai --</option>
                                                <option value="1">1 - Sangat Rendah</option>
                                                <option value="2">2 - Rendah</option>
                                                <option value="3">3 - Sedang</option>
                                                <option value="4">4 - Tinggi</option>
                                                <option value="5">5 - Sangat Tinggi</option>
                                            </select>
                                            <p class="hidden text-red-500 text-xs mt-1 font-medium error-msg">⚠️ Kriteria ini wajib dinilai!</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

                <div class="bg-white p-5 border border-gray-200 flex flex-col sm:flex-row items-center justify-between rounded-xl shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] mt-8 sticky bottom-6 z-10 gap-4">
                    <p class="text-xs text-gray-500 font-medium" id="statusText">Pastikan semua kriteria pada semua aktivitas telah dinilai.</p>
                    <button type="submit" id="btnSubmit" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 hover:shadow-lg focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Proses Perhitungan 
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div id="modal-back" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center border-b border-gray-50">
                <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-amber-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Keluar dari Penilaian?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Anda belum memproses perhitungan ini. Yakin ingin kembali ke Dashboard? <br><br>
                    <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded inline-block mt-1">💡 Tenang, pilihan Anda sudah tersimpan otomatis sebagai draft.</span>
                </p>
            </div>
            <div class="bg-gray-50 p-6 flex flex-col gap-3 rounded-b-2xl">
                <a href="{{ route('manager.dashboard') }}" class="w-full px-5 py-3.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md transition-all flex justify-center items-center text-center">
                    Ya, Kembali ke Dashboard
                </a>
                <button type="button" onclick="closeBackModal()" class="w-full px-5 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all">
                    Batal, Lanjutkan Penilaian
                </button>
            </div>
        </div>
    </div>

    <script>
        function openBackModal() {
            document.getElementById('modal-back').classList.remove('hidden');
        }
        function closeBackModal() {
            document.getElementById('modal-back').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formEvaluate');
            const selects = form.querySelectorAll('.eval-select');
            const indicator = document.getElementById('save-indicator');
            
            // Bikin Kunci Unik untuk nyimpen draft di browser khusus buat Audit ini
            const storageKey = 'draft_audit_saw_' + '{{ $audit->id }}';
            
            // 1. LOAD DRAFT (Kalau ada)
            const savedDraft = JSON.parse(localStorage.getItem(storageKey) || '{}');
            let hasDraft = false;

            selects.forEach(select => {
                const inputName = select.name;
                
                // Kalau ada di draft, isi otomatis!
                if (savedDraft[inputName]) {
                    select.value = savedDraft[inputName];
                    hasDraft = true;
                }

                // 2. AUTO-SAVE SETIAP KALI MEMILIH
                select.addEventListener('change', function() {
                    // Simpan ke Object & LocalStorage
                    savedDraft[inputName] = this.value;
                    localStorage.setItem(storageKey, JSON.stringify(savedDraft));

                    // Munculin animasi "Draft Tersimpan"
                    indicator.classList.remove('hidden');
                    indicator.classList.add('flex');
                    setTimeout(() => {
                        indicator.classList.add('hidden');
                        indicator.classList.remove('flex');
                    }, 2000);

                    // Hapus warna merah kalau udah diisi
                    this.classList.remove('border-red-500', 'ring-red-500', 'bg-red-50');
                    this.classList.add('border-gray-300');
                    this.nextElementSibling.classList.add('hidden');
                });
            });

            // Tampilkan indikator hijau sebentar kalau pas loading ada draft yang ke-load
            if(hasDraft) {
                indicator.innerHTML = '✨ Draft sebelumnya dipulihkan!';
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
                setTimeout(() => {
                    indicator.classList.add('hidden');
                    indicator.classList.remove('flex');
                    indicator.innerHTML = '<svg class="w-4 h-4 mr-1 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Draft Tersimpan!';
                }, 3000);
            }

            // 3. VALIDASI SAAT TOMBOL SUBMIT DITEKAN
            form.addEventListener('submit', function(e) {
                let allFilled = true;
                let firstEmptySelect = null;

                selects.forEach(select => {
                    if (select.value === "") {
                        allFilled = false;
                        
                        // Bikin kotaknya merah mencolok
                        select.classList.remove('border-gray-300');
                        select.classList.add('border-red-500', 'ring-1', 'ring-red-500', 'bg-red-50');
                        select.nextElementSibling.classList.remove('hidden'); // Munculin teks error

                        if (!firstEmptySelect) {
                            firstEmptySelect = select;
                        }
                    }
                });

                if (!allFilled) {
                    // Cegah form terkirim
                    e.preventDefault();
                    
                    // Alert & Auto Scroll
                    alert('Tunggu! Masih ada kriteria yang belum Anda nilai. Silakan lengkapi kotak yang berwarna merah.');
                    firstEmptySelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstEmptySelect.focus();
                } else {
                    // BERHASIL! Hapus draft di browser biar kalau buka evaluasi baru nggak bentrok
                    localStorage.removeItem(storageKey);
                    
                    // Ubah tombol jadi loading
                    const btn = document.getElementById('btnSubmit');
                    btn.innerHTML = 'Sedang Memproses...';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });
        });
    </script>
</x-app-layout>