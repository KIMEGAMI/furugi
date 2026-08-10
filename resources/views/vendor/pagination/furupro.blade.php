@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-bold text-slate-700">
            {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}件 / 全{{ $paginator->total() }}件
        </p>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-400 shadow-sm" aria-disabled="true">
                    前へ
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-900 shadow-sm hover:bg-slate-100">
                    前へ
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-400 shadow-sm" aria-disabled="true">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span class="rounded-lg border border-red-700 bg-red-600 px-4 py-2 text-sm font-black text-white shadow-sm" aria-current="page">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-900 shadow-sm hover:bg-slate-100">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-900 shadow-sm hover:bg-slate-100">
                    次へ
                </a>
            @else
                <span class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-400 shadow-sm" aria-disabled="true">
                    次へ
                </span>
            @endif
        </div>
    </nav>
@endif
