@php
    $pageSeo = config('seo.pages')['legal.faq'] ?? [];
    $faqs = [
        ['FreeプランとPremiumプランの違いは何ですか？', 'Freeプランは無料で、商品登録50件、カテゴリ登録5件まで利用できます。Premiumプランは月額480円で、商品登録数とカテゴリ数の制限がなくなり、CSV管理、売上分析、ジャンル別分析、重複チェックなどの機能を利用できます。'],
        ['CSVで一括登録できますか？', 'できます。古着システム用CSV、ヤフオクCSV変換、メルカリShops CSV変換などに対応しています。'],
        ['スマートフォンから商品画像を登録できますか？', 'できます。商品登録画面からカメラを起動して撮影した画像を登録できます。'],
        ['重複した商品を削除できますか？', '重複チェック画面から候補を確認し、残す商品を選んで不要な重複を削除できます。'],
        ['アカウントを削除すると画像も削除されますか？', 'ユーザー削除時には、そのユーザーが登録した商品画像も削除する設計です。必要なデータは事前に控えてください。'],
        ['メンテナンス中でも管理者はログインできますか？', '管理者はログイン画面から管理画面へ入り、メンテナンスモードを解除できます。'],
    ];
    $schema = [[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]],
        ])->values()->all(),
    ]];
@endphp

<x-legal-layout title="よくある質問" eyebrow="FAQ" :description="$pageSeo['description']" :schema="$schema">
    @foreach ($faqs as [$question, $answer])
        <h2>{{ $question }}</h2>
        <p>{{ $answer }}</p>
    @endforeach
</x-legal-layout>
