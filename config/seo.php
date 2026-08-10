<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'FURUPRO'),
    'title' => env('SEO_TITLE', 'FURUPRO | 古着販売の在庫管理・売上管理システム'),
    'description' => env('SEO_DESCRIPTION', 'FURUPROは、古着販売の商品登録、画像管理、SOLD管理、売上と利益の分析、CSV登録、重複チェックをまとめて行える管理システムです。'),
    'keywords' => env('SEO_KEYWORDS', '古着管理,古着販売,在庫管理,売上管理,利益管理,CSV登録,ヤフオクCSV,メルカリ管理,ラクマ管理'),
    'image' => env('SEO_IMAGE', '/images/furugi-manager-hero.png'),
    'locale' => 'ja_JP',
    'twitter_card' => 'summary_large_image',
    'updated_at' => '2026-07-21',
    'organization' => [
        'name' => env('SEO_ORGANIZATION_NAME', 'FURUPRO'),
        'url' => env('APP_URL', 'http://127.0.0.1:8000'),
        'logo' => '/images/logo.png',
    ],
    'software' => [
        'name' => 'FURUPRO',
        'category' => 'BusinessApplication',
        'operating_system' => 'Web, iOS, Android',
        'default_price' => 0,
        'currency' => 'JPY',
    ],
    'pages' => [
        'home' => ['title' => 'FURUPRO | 古着販売の在庫管理・売上管理システム', 'description' => '古着販売の商品登録、画像管理、SOLD管理、売上と利益の分析、CSV登録、重複チェックをひとつにまとめる管理システムです。', 'changefreq' => 'weekly', 'priority' => '1.0'],
        'marketing.features' => ['title' => '機能一覧 | 古着販売の商品管理・CSV登録・売上分析', 'description' => '画像付き商品管理、SOLD管理、CSV登録、ヤフオクCSV変換、売上と利益の分析、重複チェック、PWA対応をまとめて確認できます。', 'changefreq' => 'monthly', 'priority' => '0.9'],
        'marketing.use-cases' => ['title' => '活用例 | メルカリ・ヤフオク・ラクマの古着在庫管理', 'description' => '複数販路で古着を販売する方向けに、在庫管理、売上管理、CSV登録、利益確認の活用例を紹介します。', 'changefreq' => 'monthly', 'priority' => '0.9'],
        'marketing.pricing' => ['title' => '料金 | FURUPRO', 'description' => 'FURUPROの料金体系です。Freeは商品登録50件・カテゴリ5件まで、Premiumは月額480円（税込）で登録制限なし、CSV登録、売上分析、ジャンル別分析などを利用できます。', 'changefreq' => 'monthly', 'priority' => '0.8'],
        'register' => ['title' => 'アカウント作成 | FURUPRO', 'description' => 'FURUPROのアカウントを作成して、古着販売の商品管理、画像管理、SOLD管理、売上管理を始められます。', 'changefreq' => 'monthly', 'priority' => '0.7'],
        'legal.faq' => ['title' => 'よくある質問 | FURUPROのFAQ', 'description' => 'FURUPROの使い方、CSV登録、画像管理、売上分析、アカウント管理、セキュリティに関するよくある質問です。', 'changefreq' => 'monthly', 'priority' => '0.8'],
        'legal.terms' => ['title' => '利用規約 | FURUPRO', 'description' => 'FURUPROの利用条件、アカウント管理、禁止事項、データ管理、免責事項について定めた利用規約です。', 'changefreq' => 'yearly', 'priority' => '0.5'],
        'legal.privacy' => ['title' => 'プライバシーポリシー | FURUPRO', 'description' => 'FURUPROにおける個人情報、登録データ、アクセス情報の取り扱い方針です。', 'changefreq' => 'yearly', 'priority' => '0.5'],
        'legal.commercial' => ['title' => '特定商取引法に基づく表記 | FURUPRO', 'description' => 'FURUPROのFreeプラン、Premium月額480円（税込）、支払い方法、解約、返金、お問い合わせ先についての表記です。', 'changefreq' => 'yearly', 'priority' => '0.5'],
        'legal.contact' => ['title' => 'お問い合わせ | FURUPRO', 'description' => 'FURUPROへのお問い合わせページです。使い方、不具合、アカウント、個人情報の取り扱いに関するご連絡を受け付けます。', 'changefreq' => 'yearly', 'priority' => '0.5'],
    ],
];
