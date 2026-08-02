<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'トップス' => ['Tシャツ', 'シャツ', 'スウェット', 'パーカー', 'ニット'],
            'ボトムス' => ['デニム', 'チノパン', 'スラックス', 'ショートパンツ'],
            'アウター' => ['Gジャン', 'レザージャケット', 'ダウン', 'コート', 'ナイロンジャケット'],
            'シューズ' => ['スニーカー', 'ブーツ', '革靴', 'サンダル'],
            'バッグ' => ['リュック', 'トートバッグ', 'ショルダーバッグ', 'ボストンバッグ'],
            'アクセサリー' => ['帽子', 'ベルト', '腕時計', 'ネックレス'],
            'その他' => ['その他'],
        ];

        $parentOrder = 0;

        foreach ($categories as $parentName => $children) {
            $parent = Category::query()->updateOrCreate(
                ['parent_id' => null, 'name' => $parentName],
                ['sort_order' => $parentOrder]
            );

            foreach ($children as $childOrder => $childName) {
                Category::query()->updateOrCreate(
                    ['parent_id' => $parent->id, 'name' => $childName],
                    ['sort_order' => $childOrder]
                );
            }

            $parentOrder++;
        }
    }
}
