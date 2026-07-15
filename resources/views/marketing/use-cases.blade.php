<x-marketing-layout
    title="活用例 | メルカリ・ヤフオク・フリマ販売の古着在庫管理"
    description="メルカリ、ヤフオク、ラクマなど複数の販売先で古着を販売する方向けに、FURUGIの在庫管理・売上管理の活用例を紹介します。"
>
    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-black tracking-[0.24em] text-cyan-200">USE CASES</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight md:text-5xl">複数の販売先に出す古着在庫を、迷わず管理。</h1>
            <p class="mt-5 max-w-3xl text-base font-semibold leading-8 text-slate-200">メルカリ、ヤフオク、ラクマ、PayPayフリマなど、販売先が増えるほど商品情報は散らかりやすくなります。FURUGIは日々の販売管理をひとつにまとめます。</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto grid max-w-7xl gap-5 px-4 sm:px-6 lg:px-8">
            @foreach ([
                ['フリマ販売の在庫管理', '出品中の商品、SOLDの商品、仕入れ価格、販売価格を一覧で確認できます。'],
                ['仕入れ後の商品整理', '撮影した画像と商品タイトルを登録し、ジャンル別に整理できます。'],
                ['月次の売上確認', '売上、手数料、送料、利益を確認し、販売状況を振り返れます。'],
                ['次の仕入れ判断', 'ジャンル別分析を見て、どのカテゴリに力を入れるか判断できます。'],
            ] as [$heading, $body])
                <article class="rounded-lg border border-slate-200 p-6">
                    <h2 class="text-xl font-black">{{ $heading }}</h2>
                    <p class="mt-3 font-semibold leading-7 text-slate-600">{{ $body }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-marketing-layout>
