<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden" style="display: none;"></div>

<aside :class="[ sidebarOpen ? 'translate-x-0' : '-translate-x-full', isMini ? 'lg:w-20' : 'lg:w-64' ]" class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 transition-all duration-300 transform bg-white border-r border-gray-200 lg:translate-x-0 lg:static lg:inset-0 shadow-lg lg:shadow-none overflow-hidden">
    
    <div class="flex items-center h-16 border-b border-gray-200 shrink-0 bg-white transition-all duration-300" :class="isMini ? 'justify-center px-0' : 'justify-start px-6'">
        <div class="flex items-center gap-2 text-lg font-black tracking-wide text-gray-800 uppercase" :title="isMini ? 'Manajemen Audit' : ''">
            <svg class="w-7 h-7 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <span x-show="!isMini" class="whitespace-nowrap">Manajemen<span class="text-indigo-600">Audit</span></span>
        </div>
    </div>

    <nav class="flex-1 py-6 space-y-2 overflow-y-auto bg-white overflow-x-hidden transition-all duration-300" :class="isMini ? 'px-2' : 'px-4'">
        
        <p x-show="!isMini" class="px-2 mb-3 text-xs font-bold tracking-wider uppercase text-gray-400 whitespace-nowrap">Menu Utama</p>
        <div x-show="isMini" class="mb-3 border-b-2 border-gray-100 w-6 mx-auto rounded-full" style="display: none;"></div>
        
        @php $isDashboard = request()->is('*dashboard*'); @endphp
        <a href="{{ auth()->user()->role === 'auditor' ? route('auditor.dashboard') : route('manager.dashboard') }}" class="flex items-center py-3 text-sm font-bold transition-all duration-200 rounded-lg {{ $isDashboard ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}" :class="isMini ? 'justify-center px-0' : 'px-4'" title="Dashboard">
            <svg class="w-6 h-6 shrink-0 {{ $isDashboard ? 'text-indigo-600' : 'text-gray-400' }}" :class="isMini ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span x-show="!isMini" class="whitespace-nowrap">Dashboard</span>
        </a>

        @if(auth()->user()->role === 'auditor')
            @php $isAuditCreate = request()->routeIs('auditor.audit.create'); @endphp
            <a href="{{ route('auditor.audit.create') }}" class="flex items-center py-3 text-sm font-bold transition-all duration-200 rounded-lg {{ $isAuditCreate ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}" :class="isMini ? 'justify-center px-0' : 'px-4'" title="Mulai Audit Baru">
                <svg class="w-6 h-6 shrink-0 {{ $isAuditCreate ? 'text-indigo-600' : 'text-gray-400' }}" :class="isMini ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span x-show="!isMini" class="whitespace-nowrap">Mulai Audit Baru</span>
            </a>
        @endif

        @if(auth()->user()->role === 'manajer')
            @php $isCriteria = request()->routeIs('manager.criteria.*'); @endphp
            <a href="{{ route('manager.criteria.index') }}" class="flex items-center py-3 text-sm font-bold transition-all duration-200 rounded-lg {{ $isCriteria ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}" :class="isMini ? 'justify-center px-0' : 'px-4'" title="Manajemen Kriteria">
                <svg class="w-6 h-6 shrink-0 {{ $isCriteria ? 'text-indigo-600' : 'text-gray-400' }}" :class="isMini ? 'mr-0' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span x-show="!isMini" class="whitespace-nowrap">Manajemen Kriteria</span>
            </a>
        @endif
    </nav>

    <div class="py-4 border-t border-gray-200 shrink-0 bg-gray-50 transition-all duration-300" :class="isMini ? 'px-2' : 'px-4'">
        
        <div class="flex items-center mb-4 transition-all duration-300" :class="isMini ? 'justify-center px-0' : 'gap-3 px-1'">
            <div class="flex items-center justify-center w-10 h-10 text-sm font-black text-indigo-700 bg-indigo-100 border border-indigo-200 rounded-full shadow-sm shrink-0" :title="isMini ? '{{ auth()->user()->name }}' : ''">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div x-show="!isMini" class="overflow-hidden">
                <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] tracking-widest text-gray-500 uppercase truncate">{{ auth()->user()->role }}</p>
            </div>
        </div>
        
        <form id="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmLogout()" class="flex items-center justify-center w-full py-2.5 text-sm font-bold transition-colors rounded-lg text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 hover:text-red-600 hover:border-red-200 group" :class="isMini ? 'px-0' : 'px-4'" title="Keluar Aplikasi">
                <svg class="w-5 h-5 shrink-0 text-gray-400 group-hover:text-red-500 transition-colors" :class="isMini ? 'mr-0' : 'mr-2'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span x-show="!isMini" class="whitespace-nowrap">Keluar Aplikasi</span>
            </button>
        </form>

    </div>
    
</aside>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin mau keluar?',
            text: "Sesi Anda akan berakhir dan Anda perlu login kembali.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',  
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>