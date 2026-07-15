<x-marketing-layout
    title="FURUGIの機能 | 古着販売の在庫管理・SOLD管理・売上分析"
    description="FURUGIの機能一覧。古着販売の商品登録、画像管理、SOLD管理、CSV取込、売上分析、ジャンル別分析をまとめて確認できます。"
>
    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-black tracking-[0.24em] text-cyan-200">FEATURES</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-black leading-tight md:text-5xl">古着販売の在庫管理に必要な機能をひとつに。</h1>
            <p class="mt-5 max-w-3xl text-base font-semibold leading-8 text-slate-200">FURUGIは、フリマ販売や複数サイト出品で増えやすい商品情報を整理し、販売状況と利益を追いやすくする管理システムです。</p>
        </div>
    </section>

    <section class="py-14">
        <div class="mx-auto grid max-w-7xl gap-5 px-4 sm:px-6 md:grid-cols-2 lg:grid-cols-3 lg:px-8">
            @foreach ([
                ['商品登録', '管理ID、商品タイトル、ジャンル、仕入れ価格、販売価格、送料、販売手数料率を登録できます。'],
                ['画像管理', '商品画像を登録できます。スマホPWAではカメラ撮影からそのまま画像を掲載できます。'],
                ['SOLD管理', '販売済みの商品をSOLDへ切り替え、売上や利益の計算に反映できます。'],
                ['CSV取込', 'PremiumではCSVで商品をまとめて登録でき、登録作業を短縮できます。'],
                ['CSV出力', '売上データをCSVで出力し、月次管理や確定申告前の整理に活用できます。'],
                ['ジャンル別分析', 'どのジャンルが売れているかを確認し、次の仕入れ判断に役立てられます。'],
            ] as [$heading, $body])
                <article class="rounded-lg border border-slate-200 p-6">
                    <h2 class="text-xl font-black">{{ $heading }}</h2>
                    <p class="mt-3 font-semibold leading-7 text-slate-600">{{ $body }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-marketing-layout>
