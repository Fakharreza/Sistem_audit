<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-800">Mulai Sesi Audit Baru</h2>
                    <p class="text-sm text-gray-500 mt-1">Buat sesi audit COBIT 2019 baru dan pilih domain yang akan dievaluasi.</p>
                </div>
                <a href="{{ route('auditor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 sm:p-8">
                
                <form action="{{ route('auditor.audit.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama / Kode Audit</label>
                        <input type="text" name="audit_code" value="{{ old('audit_code') }}" placeholder="Contoh: Audit Tata Kelola Q1 2026" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        
                        @error('audit_code')
                            <p class="text-red-500 text-xs italic mt-2 font-bold">⚠️ {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pelaksanaan</label>
                        <input type="date" name="audit_date" value="{{ old('audit_date') }}" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Domain COBIT 2019 yang Dievaluasi:</label>
                        <div class="bg-slate-50 p-5 rounded-xl border border-gray-200">
                            @foreach($domains as $domain)
                                <div class="flex items-center mb-3 last:mb-0 hover:bg-slate-100 p-2 rounded-lg transition-colors cursor-pointer">
                                    <input type="checkbox" name="domain_ids[]" value="{{ $domain->id }}" id="domain_{{ $domain->id }}" class="w-5 h-5 text-indigo-600 bg-white border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                                    <label for="domain_{{ $domain->id }}" class="ml-3 text-sm font-medium text-gray-900 cursor-pointer w-full">
                                        <span class="font-bold text-indigo-700">{{ $domain->code }}</span> - {{ $domain->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2 font-medium">*Kamu bisa memilih lebih dari satu domain sekaligus.</p>
                    </div>

                    <div class="flex justify-end pt-5 border-t border-gray-100">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Buat Sesi Audit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>