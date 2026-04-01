<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mulai Sesi Audit Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nama / Kode Audit</label>
                        <input type="text" name="audit_code" placeholder="Contoh: Audit Tata Kelola Q1 2026" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
                        <input type="date" name="audit_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Domain COBIT 2019 yang Dievaluasi:</label>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            @foreach($domains as $domain)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="domain_ids[]" value="{{ $domain->id }}" id="domain_{{ $domain->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="domain_{{ $domain->id }}" class="ml-2 text-sm font-medium text-gray-900">
                                        <span class="font-bold">{{ $domain->code }}</span> - {{ $domain->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">*Kamu bisa memilih lebih dari satu domain.</p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Buat Sesi Audit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>