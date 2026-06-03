<header class="flex items-center justify-between px-4 h-16 bg-white border-b border-gray-200 shadow-sm shrink-0 sm:px-6 lg:px-8">
    
    <div class="flex items-center gap-4">
        <div class="flex items-center lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-500 transition-colors rounded-md hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <div class="hidden lg:flex items-center">
            <button @click="isMini = !isMini" class="p-2 text-gray-500 transition-colors rounded-md hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
        </div>

        <div class="hidden sm:flex items-center text-sm font-bold text-gray-500">
            <span class="px-3 py-1.5 bg-slate-50 rounded-lg border border-gray-200 shadow-sm flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                Sistem Tata Kelola IT
            </span>
        </div>
    </div>

    <div class="flex items-center">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-gray-600 transition-all duration-200 bg-white border border-gray-200 rounded-lg shadow-sm hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="hidden sm:inline">Pengaturan Profil</span>
        </a>
    </div>

</header>