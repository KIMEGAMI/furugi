<nav x-data="{ open: false }" class="border-b border-cyan-300/20 bg-slate-950/45 text-white shadow-2xl backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}">
                        <span class="text-xl font-black text-white">FURUGI</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        HOME
                    </x-nav-link>

                    <x-nav-link :href="route('auction-items.index')" :active="request()->routeIs('auction-items.*')">
                        商品一覧
                    </x-nav-link>

                    <x-nav-link :href="route('auction-items.create')" :active="request()->routeIs('auction-items.create')">
                        商品登録
                    </x-nav-link>

                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                        売上管理
                    </x-nav-link>

                    <x-nav-link :href="route('category-sales.index')" :active="request()->routeIs('category-sales.*')">
                        ジャンル別売上
                    </x-nav-link>

                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        プロフィール
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <div class="text-sm font-bold text-slate-200">
                    {{ Auth::user()->name }}
                </div>

                <form method="POST" action="{{ route('logout') }}" class="ms-6">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600"
                    >
                        ログアウト
                    </button>
                </form>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-md p-2 text-slate-300 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-slate-300 focus:bg-gray-100 focus:text-slate-300 focus:outline-none"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                HOME
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('auction-items.index')" :active="request()->routeIs('auction-items.*')">
                商品一覧
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('auction-items.create')" :active="request()->routeIs('auction-items.create')">
                商品登録
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                売上管理
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('category-sales.index')" :active="request()->routeIs('category-sales.*')">
                ジャンル別売上
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                プロフィール
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-cyan-300/20 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-white">
                    {{ Auth::user()->name }}
                </div>

                <div class="text-sm font-medium text-slate-300">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();">
                        ログアウト
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
