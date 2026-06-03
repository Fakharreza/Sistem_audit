<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 px-2">
                <h2 class="text-2xl font-black text-gray-800">Dashboard Auditor</h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali! Kelola dan pantau seluruh sesi audit COBIT 2019 Anda di sini.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Riwayat Audit Saya</h3>
                            <p class="text-sm text-gray-500 mt-1">Daftar seluruh sesi audit yang pernah Anda buat atau sedang berjalan.</p>
                        </div>
                        <a href="{{ route('auditor.audit.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition-colors flex items-center gap-2 text-sm w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Buat Sesi Audit Baru
                        </a>
                    </div>

                    <form method="GET" action="{{ route('auditor.dashboard') }}" class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col md:flex-row gap-4">
                        
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Cari Kode Audit</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kode..." class="w-full pl-10 pr-4 py-2 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="w-full md:w-48">
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Status</label>
                            <select name="status" class="w-full py-2 px-3 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft (Proses)</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div class="w-full md:w-32">
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Tahun</label>
                            <select name="year" class="w-full py-2 px-3 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                <option value="">Semua</option>
                                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2 w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-lg shadow-sm transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'year']))
                                <a href="{{ route('auditor.dashboard') }}" class="px-3 py-2 bg-white hover:bg-gray-100 text-gray-600 text-sm font-bold rounded-lg transition-colors border border-gray-300 shadow-sm flex items-center justify-center" title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">Kode Audit</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Tanggal</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Status</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($audits as $audit)
                                    <tr class="bg-white hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-indigo-900 whitespace-nowrap">
                                            {{ $audit->audit_code }}
                                        </td>
                                        <td class="px-6 py-4 font-medium">
                                            {{ \Carbon\Carbon::parse($audit->audit_date)->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center">
                                                @if($audit->status === 'completed')
                                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center w-max">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        COMPLETED
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-700 border border-amber-200 flex items-center w-max">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        DRAFT
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 flex justify-center">
                                            @if($audit->status === 'draft')
                                                <a href="{{ route('auditor.audit.kuesioner', $audit->id) }}" class="text-white bg-indigo-600 hover:bg-indigo-700 font-bold rounded-lg text-xs px-4 py-2 transition shadow-sm flex items-center gap-1.5 w-max">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Lanjutkan Kuesioner
                                                </a>
                                            @else
                                                <a href="{{ route('auditor.audit.kuesioner', $audit->id) }}" class="text-slate-600 bg-slate-50 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 font-bold rounded-lg text-xs px-4 py-2 transition-all duration-200 shadow-sm flex items-center gap-1.5 w-max">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    Lihat Riwayat
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <p class="font-medium text-gray-600">Belum ada data audit yang ditemukan.</p>
                                                <p class="text-sm mt-1">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 pagination-theme">
                        {{ $audits->links() }}
                    </div>

                    <style>
                        /* Warna tombol halaman yang sedang aktif (Indigo) */
                        .pagination-theme nav[role="navigation"] [aria-current="page"] span {
                            background-color: #4f46e5 !important; 
                            color: white !important;
                            border-color: #4f46e5 !important;
                            font-weight: 900 !important;
                        }
                        
                        /* Warna tombol angka/panah biasa (Putih) */
                        .pagination-theme nav[role="navigation"] a {
                            background-color: white !important;
                            color: #6b7280 !important;
                            border-color: #e5e7eb !important;
                            font-weight: 600 !important;
                            transition: all 0.2s ease-in-out;
                        }
                        
                        /* Warna saat tombol di-hover/disentuh mouse (Biru Muda) */
                        .pagination-theme nav[role="navigation"] a:hover {
                            background-color: #eef2ff !important; 
                            color: #4f46e5 !important;
                            border-color: #c7d2fe !important;
                        }
                        
                        /* Warna tombol panah kalau udah mentok / disable (Abu-abu pudar) */
                        .pagination-theme nav[role="navigation"] span[aria-disabled="true"] span {
                            background-color: #f9fafb !important;
                            color: #d1d5db !important;
                            border-color: #f3f4f6 !important;
                        }
                    </style>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>