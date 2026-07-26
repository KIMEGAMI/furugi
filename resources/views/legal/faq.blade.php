@php
    $pageSeo = config('seo.pages')['legal.faq'] ?? [];
    $faqs = [
        ['Freeプランでは何ができますか？', '商品登録、画像付き商品管理、SOLD管理、基本ダッシュボードを利用できます。まず操作感を試したい方向けのプランです。'],
        ['Premiumプランでは何が増えますか？', '商品登録数の上限拡張、CSV登録、CSV出力、売上・利益分析、ジャンル別分析、運用診断レポートを利用できます。'],
        ['料金はいくらですか？', 'Premiumプランは月額480円を想定しています。実際の金額は決済画面に表示される内容を確認してください。'],
        ['画像の容量制限はありますか？', '商品画像は1枚あたり2MBまでを想定しています。JPG、PNG、WebPなど一般的な画像形式に対応します。'],
        ['スマホから商品画像を登録できますか？', 'スマホやPWAで利用する場合、商品登録画面からカメラを起動して撮影した画像を登録できます。'],
        ['CSVで一括登録できますか？', 'Premium向けにCSV登録を用意しています。古着システムCSV、ヤフオクCSV変換などに対応しています。'],
        ['重複した商品を削除できますか？', '重複チェック画面から重複候補を確認し、最新の商品を残して不要な重複分を削除できます。'],
        ['アカウントを削除すると画像も削除されますか？', 'ユーザー削除時には、そのユーザーが登録した商品画像も削除する設計にしています。必要なデータは事前に控えてください。'],
        ['メンテナンス中でも管理者はログインできますか？', '管理者はメンテナンス中でもログインページから管理画面へ入り、メンテナンスモードを解除できます。'],
        ['検索順位は必ず上がりますか？', 'SEO設定は検索エンジンに理解されやすくするための対策です。順位は競合、被リンク、運用コンテンツ、Search Console登録状況にも影響されます。'],
    ];
    $schema = [[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq[0],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq[1],
            ],
        ])->values()->all(),
    ]];
@endphp

<x-legal-layout
    title="よくある質問"
    eyebrow="FAQ"
    :description="$pageSeo['description']"
    :schema="$schema"
>
    <h2>プランについて</h2>

    @foreach (array_slice($faqs, 0, 3) as [$question, $answer])
        <h3>{{ $question }}</h3>
        <p>{{ $answer }}</p>
    @endforeach

    <h2>商品登録と画像について</h2>

    @foreach (array_slice($faqs, 3, 2) as [$question, $answer])
        <h3>{{ $question }}</h3>
        <p>{{ $answer }}</p>
    @endforeach

    <h2>CSV・売上管理について</h2>

    @foreach (array_slice($faqs, 5, 2) as [$question, $answer])
        <h3>{{ $question }}</h3>
        <p>{{ $answer }}</p>
    @endforeach

    <h2>アカウントと運用について</h2>

    @foreach (array_slice($faqs, 7) as [$question, $answer])
        <h3>{{ $question }}</h3>
        <p>{{ $answer }}</p>
    @endforeach
</x-legal-layout>
