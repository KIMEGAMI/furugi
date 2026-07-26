<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-8 text-white">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-black tracking-[0.18em] text-amber-300">NOTICE BOARD</p>
                    <h1 class="mt-2 text-3xl font-black">お知らせ一覧</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex w-fit rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-black text-white hover:bg-white/20">
                    ダッシュボードへ戻る
                </a>
            </div>

            <section class="rounded-2xl border border-amber-300 bg-black p-5 shadow-2xl">
                @if ($notices->count() > 0)
                    <div class="divide-y divide-white/15">
                        @foreach ($notices as $notice)
                            <a href="{{ route('notices.show', $notice) }}" class="block py-4 transition hover:bg-white/10">
                                <div class="flex flex-col gap-1 px-2 md:flex-row md:items-center md:justify-between">
                                    <h2 class="text-base font-black text-white">{{ $notice->title }}</h2>
                                    <time datetime="{{ $notice->published_at?->toDateString() }}" class="text-xs font-bold text-amber-200">
                                        {{ $notice->published_at?->format('Y/m/d') }}
                                    </time>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($notices->hasPages())
                        <nav class="mt-6 flex flex-wrap items-center justify-center gap-2" aria-label="お知らせ一覧のページ送り">
                            @if ($notices->onFirstPage())
                                <span class="rounded-lg border border-white/15 bg-white/5 px-3 py-2 text-sm font-black text-slate-500">←</span>
                            @else
                                <a href="{{ $notices->previousPageUrl() }}" class="rounded-lg border border-white/25 bg-white/10 px-3 py-2 text-sm font-black text-white hover:bg-white/20" aria-label="前のページ">←</a>
                            @endif

                            @foreach ($notices->getUrlRange(1, $notices->lastPage()) as $page => $url)
                                @if ($page === $notices->currentPage())
                                    <span class="rounded-lg bg-amber-300 px-3 py-2 text-sm font-black text-black" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="rounded-lg border border-white/25 bg-white/10 px-3 py-2 text-sm font-black text-white hover:bg-white/20">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($notices->hasMorePages())
                                <a href="{{ $notices->nextPageUrl() }}" class="rounded-lg border border-white/25 bg-white/10 px-3 py-2 text-sm font-black text-white hover:bg-white/20" aria-label="次のページ">→</a>
                            @else
                                <span class="rounded-lg border border-white/15 bg-white/5 px-3 py-2 text-sm font-black text-slate-500">→</span>
                            @endif
                        </nav>
                    @endif
                @else
                    <p class="rounded-xl border border-white/15 bg-white/10 p-5 text-sm font-bold text-white">
                        現在、掲載中のお知らせはありません。
                    </p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
