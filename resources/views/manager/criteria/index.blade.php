<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kriteria SAW') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
            
            <div class="w-full md:w-2/3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    
                    <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Kriteria</h3>
                            <p class="text-sm text-gray-500">Total Bobot Saat Ini: 
                                <span class="font-bold {{ $totalWeight == 1 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $totalWeight }} (Idealnya 1.0)
                                </span>
                            </p>
                        </div>
                        
                        <form action="{{ route('manager.criteria.reset') }}" method="POST" onsubmit="return confirm('Yakin ingin mereset ke 3 Kriteria awal? Semua modifikasi kriteria saat ini akan ditimpa.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center gap-2">
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
                                <thead class="bg-gray-100 text-gray-700 uppercase font-bold text-xs">
                                    <tr>
                                        <th class="px-6 py-4">Nama Kriteria</th>
                                        <th class="px-6 py-4 text-center">Tipe Atribut</th>
                                        <th class="px-6 py-4 text-center">Bobot (W)</th>
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criteria as $item)
                                        <tr class="border-b hover:bg-gray-50">
                                            
                                            <td class="px-4 py-3">
                                                <input type="text" name="criteria[{{ $item->id }}][name]" value="{{ $item->name }}" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                            </td>
                                            
                                            <td class="px-4 py-3 text-center">
                                                <select name="criteria[{{ $item->id }}][type]" class="text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                                    <option value="benefit" {{ $item->type == 'benefit' ? 'selected' : '' }}>Benefit</option>
                                                    <option value="cost" {{ $item->type == 'cost' ? 'selected' : '' }}>Cost</option>
                                                </select>
                                            </td>
                                            
                                            <td class="px-4 py-3 text-center">
                                                <input type="number" step="0.01" name="criteria[{{ $item->id }}][weight]" value="{{ $item->weight }}" class="w-24 text-sm text-center border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                            </td>
                                            
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" onclick="if(confirm('Yakin ingin menghapus kriteria ini?')) document.getElementById('delete-form-{{ $item->id }}').submit();" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded hover:bg-red-200 transition-colors">
                                                    Hapus
                                                </button>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Simpan Semua Perubahan
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
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Tambah Kriteria Baru</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('manager.criteria.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kriteria</label>
                                <input type="text" name="name" required placeholder="Contoh: Dampak Operasional" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Atribut</label>
                                <select name="type" required class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500">
                                    <option value="benefit">Benefit (Makin besar makin baik)</option>
                                    <option value="cost">Cost (Makin kecil makin baik)</option>
                                </select>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Bobot (W)</label>
                                <input type="number" step="0.01" name="weight" required placeholder="Contoh: 0.15" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white font-bold rounded-lg shadow-sm hover:bg-emerald-700 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Tambah Kriteria
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>