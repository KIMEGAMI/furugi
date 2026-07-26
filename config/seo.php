<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'FURUGI MANAGER'),
    'title' => env('SEO_TITLE', 'FURUGI MANAGER | 古着販売の在庫管理・売上管理システム'),
    'description' => env('SEO_DESCRIPTION', 'FURUGI MANAGERは、古着販売の商品登録、画像管理、SOLD管理、売上・利益分析、CSV登録、重複チェックをまとめて行える古着ビジネス向け管理システムです。'),
    'keywords' => env('SEO_KEYWORDS', '古着管理,古着販売,在庫管理,売上管理,利益管理,CSV登録,ヤフオクCSV,メルカリ管理,ラクマ管理,フリマ在庫管理'),
    'image' => env('SEO_IMAGE', '/images/furugi-manager-hero.png'),
    'locale' => 'ja_JP',
    'twitter_card' => 'summary_large_image',
    'updated_at' => '2026-07-21',
    'organization' => [
        'name' => env('SEO_ORGANIZATION_NAME', 'FURUGI MANAGER'),
        'url' => env('APP_URL', 'http://127.0.0.1:8000'),
        'logo' => '/images/logo.png',
    ],
    'software' => [
        'name' => 'FURUGI MANAGER',
        'category' => 'BusinessApplication',
        'operating_system' => 'Web, iOS, Android',
        'price_currency' => 'JPY',
        'default_price' => 480,
    ],
    'pages' => [
        'home' => [
            'title' => 'FURUGI MANAGER | 古着販売の在庫管理・売上管理システム',
            'description' => '古着販売の商品登録、画像管理、SOLD管理、売上・利益分析、CSV登録、重複チェックをひとつにまとめる管理システムです。フリマ・ヤフオク・複数販路の運用を見やすくします。',
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
        'marketing.features' => [
            'title' => '機能一覧 | 古着販売の商品管理・CSV登録・売上分析',
            'description' => 'FURUGI MANAGERの機能一覧。画像付き商品管理、SOLD管理、CSV登録、ヤフオクCSV変換、売上・利益分析、重複チェック、PWA対応をまとめて確認できます。',
            'changefreq' => 'monthly',
            'priority' => '0.9',
        ],
        'marketing.use-cases' => [
            'title' => '活用例 | メルカリ・ヤフオク・ラクマの古着在庫管理',
            'description' => 'メルカリ、ヤフオク、ラクマなど複数販路で古着を販売する方向けに、在庫管理、売上管理、CSV登録、利益確認の活用例を紹介します。',
            'changefreq' => 'monthly',
            'priority' => '0.9',
        ],
        'marketing.pricing' => [
            'title' => '料金プラン | FreeとPremiumの違い',
            'description' => 'FURUGI MANAGERのFreeプランとPremiumプランの違い、月額料金、使える機能、CSV登録、売上分析、バックアップ・エクスポート機能の考え方を確認できます。',
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        'register' => [
            'title' => '無料アカウント作成 | FURUGI MANAGER',
            'description' => 'FURUGI MANAGERの無料アカウントを作成して、古着販売の商品管理、画像管理、SOLD管理、売上管理を始められます。',
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ],
        'legal.faq' => [
            'title' => 'よくある質問 | 古着管理システムのFAQ',
            'description' => 'FURUGI MANAGERの使い方、FreeとPremiumの違い、CSV登録、画像管理、売上分析、アカウント管理、セキュリティに関するよくある質問です。',
            'changefreq' => 'monthly',
            'priority' => '0.8',
        ],
        'legal.terms' => [
            'title' => '利用規約 | FURUGI MANAGER',
            'description' => 'FURUGI MANAGERの利用条件、アカウント管理、禁止事項、Premiumプラン、データ管理、免責事項について定めた利用規約です。',
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ],
        'legal.privacy' => [
            'title' => 'プライバシーポリシー | FURUGI MANAGER',
            'description' => 'FURUGI MANAGERにおける個人情報、登録データ、アクセス情報、決済関連情報の取り扱い方針です。',
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ],
        'legal.commercial' => [
            'title' => '特定商取引法に基づく表記 | FURUGI MANAGER',
            'description' => 'FURUGI MANAGER Premiumプランの販売価格、支払い方法、サービス提供時期、解約、返金、お問い合わせ先についての表記です。',
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ],
        'legal.contact' => [
            'title' => 'お問い合わせ | FURUGI MANAGER',
            'description' => 'FURUGI MANAGERへのお問い合わせページです。使い方、不具合、Premiumプラン、アカウント、個人情報の取り扱いに関するご連絡を受け付けます。',
            'changefreq' => 'yearly',
            'priority' => '0.5',
        ],
    ],
];
