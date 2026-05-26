<nav class="bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="古着管理システム"
                        class="h-12 w-auto"
                    >
                </a>

                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
                    >
                        HOME
                    </a>

                    <a
                        href="{{ route('auction-items.index') }}"
                        class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('auction-items.*') ? 'bg-blue-700 text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
                    >
                        出品管理
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="px-4 py-3 rounded-xl text-sm font-bold {{ request()->routeIs('profile.edit') ? 'bg-blue-700 text-white' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
                    >
                        プロフィール
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-4">
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-bold">
                        LOGIN USER
                    </p>
                    <p class="text-sm text-slate-700 font-black">
                        {{ Auth::user()->name }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow hover:bg-slate-700 transition"
                    >
                        ログアウト
                    </button>
                </form>
            </div>

            <div class="flex items-center sm:hidden">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white"
                    >
                        ログアウト
                    </button>
                </form>
            </div>
        </div>

        <div class="sm:hidden pb-4 grid grid-cols-3 gap-2">
            <a
                href="{{ route('dashboard') }}"
                class="text-center px-3 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600' }}"
            >
                HOME
            </a>

            <a
                href="{{ route('auction-items.index') }}"
                class="text-center px-3 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('auction-items.*') ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600' }}"
            >
                出品管理
            </a>

            <a
                href="{{ route('profile.edit') }}"
                class="text-center px-3 py-3 rounded-xl text-xs font-bold {{ request()->routeIs('profile.edit') ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600' }}"
            >
                プロフィール
            </a>
        </div>
    </div>
</nav>