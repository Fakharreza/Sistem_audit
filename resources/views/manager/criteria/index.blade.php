<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-200 pb-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-800">
                        Manajemen Kriteria SAW
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Atur nama, atribut, dan bobot (W) kriteria untuk evaluasi prioritas perbaikan.</p>
                </div>
                
                <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-indigo-600 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                
                <div class="w-full md:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                        
                        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Daftar Kriteria</h3>
                                <p class="text-sm text-gray-500 mt-1">Total Bobot Saat Ini: 
                                    <span id="total-bobot-container" class="font-black px-2 py-0.5 rounded {{ $totalWeight == 1 ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                        <span id="total-bobot-text">{{ $totalWeight }}</span> (Idealnya 1.0)
                                    </span>
                                </p>
                            </div>
                            
                            <form id="reset-form" action="{{ route('manager.criteria.reset') }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmReset()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-sm transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Reset ke Default
                                </button>
                            </form>
                        </div>
                        
                        <form action="{{ route('manager.criteria.updateAll') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100 text-gray-700 uppercase font-bold text-xs border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-4">Nama Kriteria</th>
                                            <th class="px-6 py-4 text-center w-32">Tipe Atribut</th>
                                            <th class="px-6 py-4 text-center w-28">Bobot (W)</th>
                                            <th class="px-6 py-4 text-center w-24">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($criteria as $item)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                
                                                <td class="px-4 py-3">
                                                    <input type="text" name="criteria[{{ $item->id }}][name]" value="{{ $item->name }}" required class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 shadow-sm font-medium text-gray-800">
                                                </td>
                                                
                                                <td class="px-4 py-3 text-center">
                                                    <select name="criteria[{{ $item->id }}][type]" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 shadow-sm font-medium {{ $item->type == 'benefit' ? 'text-emerald-700' : 'text-orange-700' }}">
                                                        <option value="benefit" {{ $item->type == 'benefit' ? 'selected' : '' }}>Benefit</option>
                                                        <option value="cost" {{ $item->type == 'cost' ? 'selected' : '' }}>Cost</option>
                                                    </select>
                                                </td>
                                                
                                                <td class="px-4 py-3 text-center">
                                                    <input type="number" step="0.01" name="criteria[{{ $item->id }}][weight]" value="{{ $item->weight }}" required class="weight-input w-full text-sm text-center border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500 shadow-sm font-black text-indigo-700">
                                                </td>
                                                
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="px-3 py-2 bg-white border border-red-200 text-red-600 text-xs font-bold rounded shadow-sm hover:bg-red-50 transition-colors flex items-center justify-center mx-auto w-full">
                                                        Hapus
                                                    </button>
                                                </td>
                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-5 bg-gray-50 border-t border-gray-200 flex justify-end">
                                <button type="submit" id="btn-simpan" class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-md hover:bg-indigo-700 transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>

                        @foreach($criteria as $item)
                            <form id="delete-form-{{ $item->id }}" action="{{ route('manager.criteria.destroy', $item->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach

                    </div>
                </div>

                <div class="w-full md:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 sticky top-6">
                        <div class="p-6 border-b border-gray-200 bg-indigo-50">
                            <h3 class="text-lg font-black text-indigo-900 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Kriteria Baru
                            </h3>
                        </div>
                        <div class="p-6">
                            <form id="add-form" action="{{ route('manager.criteria.store') }}" method="POST">
                                @csrf
                                <div class="mb-5">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Kriteria</label>
                                    <input type="text" id="new-name" name="name" required placeholder="Contoh: Dampak Operasional" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm">
                                </div>
                                <div class="mb-5">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Tipe Atribut</label>
                                    <select name="type" required class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm font-medium">
                                        <option value="benefit">Benefit (Makin besar = baik)</option>
                                        <option value="cost">Cost (Makin kecil = baik)</option>
                                    </select>
                                </div>
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Bobot (W)</label>
                                    <input type="number" id="new-weight" step="0.01" name="weight" required placeholder="Contoh: 0.15" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 shadow-sm font-bold text-indigo-700">
                                </div>
                                <button type="button" id="btn-tambah" onclick="confirmAdd()" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-lg shadow-sm hover:bg-emerald-700 transition-colors flex justify-center items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tambah Kriteria
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="modal-reset" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center border-b border-gray-50">
                <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-amber-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-3 tracking-tight">Reset Kriteria?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Yakin ingin mereset ke <strong class="text-amber-600">3 Kriteria awal</strong>? Semua modifikasi, penambahan, dan penghapusan kriteria saat ini akan <span class="font-bold text-red-500">ditimpa secara permanen</span>.
                </p>
            </div>
            <div class="bg-gray-50 p-6 flex flex-col gap-3 rounded-b-2xl">
                <button type="button" onclick="executeReset()" class="w-full px-5 py-3.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md transition-all flex justify-center items-center">
                    Ya, Lakukan Reset
                </button>
                <button type="button" onclick="closeModal('modal-reset')" class="w-full px-5 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <div id="modal-delete" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center border-b border-gray-50">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-red-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-3 tracking-tight">Hapus Kriteria?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Tindakan ini tidak dapat dibatalkan. Kriteria ini tidak akan lagi digunakan dalam perhitungan SAW di masa depan.
                </p>
            </div>
            <div class="bg-gray-50 p-6 flex flex-col gap-3 rounded-b-2xl">
                <button type="button" onclick="executeDelete()" class="w-full px-5 py-3.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-md transition-all flex justify-center items-center">
                    Ya, Hapus Permanen
                </button>
                <button type="button" onclick="closeModal('modal-delete')" class="w-full px-5 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <div id="modal-add" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden transform transition-all border border-gray-100">
            <div class="p-8 text-center border-b border-gray-50">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-emerald-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-3 tracking-tight">Tambah Kriteria?</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Pastikan total seluruh bobot kriteria (termasuk yang baru ini) bernilai <strong class="text-emerald-600">tepat 1.00</strong> agar perhitungan SAW akurat.
                </p>
            </div>
            <div class="bg-gray-50 p-6 flex flex-col gap-3 rounded-b-2xl">
                <button type="button" onclick="executeAdd()" class="w-full px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition-all flex justify-center items-center">
                    Ya, Tambahkan Sekarang
                </button>
                <button type="button" onclick="closeModal('modal-add')" class="w-full px-5 py-3.5 bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-100 text-sm font-bold rounded-xl transition-all">
                    Periksa Kembali
                </button>
            </div>
        </div>
    </div>

    <script>
        let pendingDeleteId = null;

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            pendingDeleteId = null;
        }

        function confirmReset() {
            openModal('modal-reset');
        }
        function executeReset() {
            document.getElementById('reset-form').submit();
        }

        function confirmDelete(id) {
            pendingDeleteId = id;
            openModal('modal-delete');
        }
        function executeDelete() {
            if(pendingDeleteId) {
                document.getElementById('delete-form-' + pendingDeleteId).submit();
            }
        }

        function confirmAdd() {
            const inputName = document.getElementById('new-name').value;
            const inputWeight = document.getElementById('new-weight').value;
            
            if(!inputName || !inputWeight) {
                document.getElementById('add-form').reportValidity();
                return;
            }
            openModal('modal-add');
        }
        function executeAdd() {
            document.getElementById('add-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const editInputs = document.querySelectorAll('.weight-input');
            const newInput = document.getElementById('new-weight');
            const totalContainer = document.getElementById('total-bobot-container');
            const totalText = document.getElementById('total-bobot-text');
            const btnSimpan = document.getElementById('btn-simpan');
            const btnTambah = document.getElementById('btn-tambah');

            function hitungTotal() {
                let total = 0;
                
                editInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                
                total += parseFloat(newInput.value) || 0;
            
                totalText.innerText = total.toFixed(2);

                if (total > 1.00) {
                    totalContainer.className = 'font-black px-2 py-0.5 rounded bg-red-100 text-red-700 border border-red-200';
                    btnSimpan.disabled = true;
                    btnSimpan.classList.add('opacity-50', 'cursor-not-allowed');
                    btnTambah.disabled = true;
                    btnTambah.classList.add('opacity-50', 'cursor-not-allowed');
                } else if (total === 1.00) {
                    totalContainer.className = 'font-black px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 border border-emerald-200';
                    btnSimpan.disabled = false;
                    btnSimpan.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnTambah.disabled = false;
                    btnTambah.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                   
                    totalContainer.className = 'font-black px-2 py-0.5 rounded bg-amber-100 text-amber-700 border border-amber-200';
                    btnSimpan.disabled = false;
                    btnSimpan.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnTambah.disabled = false;
                    btnTambah.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            editInputs.forEach(input => {
                input.addEventListener('input', hitungTotal);
            });
            newInput.addEventListener('input', hitungTotal);
            
            hitungTotal();
        });
    </script>
</x-app-layout>