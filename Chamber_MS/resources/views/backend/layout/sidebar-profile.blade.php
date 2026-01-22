<a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-100">
    <img src="{{ asset('assets/Default_User.png') }}" class="w-10 h-10 rounded-full border" alt="User">
    <div class="flex flex-col text-sm">
        <span class="font-semibold text-gray-800">John Doe</span>
        <span class="text-gray-500">Administrator</span>
    </div>
</a>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
        class="w-full text-left flex items-center gap-2 px-3 py-2 rounded
                   hover:bg-red-100 text-red-600 font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-9V5" />
        </svg>
        <span>Logout</span>
    </button>
</form>
