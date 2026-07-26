<x-app-layout>
    <div class="min-h-screen bg-slate-950 py-8 text-white">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-black tracking-[0.18em] text-amber-300">NOTICE</p>
                    <h1 class="mt-2 text-3xl font-black">{{ $notice->title }}</h1>
                </div>
                <a href="{{ route('notices.index') }}" class="inline-flex w-fit rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-black text-white hover:bg-white/20">
                    一覧へ戻る
                </a>
            </div>

            <article class="rounded-2xl border border-amber-300 bg-black p-6 shadow-2xl">
                <time datetime="{{ $notice->published_at?->toDateString() }}" class="text-xs font-black text-amber-200">
                    {{ $notice->published_at?->format('Y/m/d H:i') }}
                </time>

                <div class="mt-5 whitespace-pre-line text-base font-bold leading-8 text-white">
                    {{ $notice->body }}
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
