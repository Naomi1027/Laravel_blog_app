<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()
            ->count(26)
            ->sequence(
                ['name' => '動物', 'key' => 'animal'],
                ['name' => 'IT', 'key' => 'IT'],
                ['name' => 'スポーツ', 'key' => 'sports'],
                ['name' => '音楽', 'key' => 'music'],
                ['name' => '本', 'key' => 'book'],
                ['name' => '映画', 'key' => 'movie'],
                ['name' => 'アニメ', 'key' => 'anime'],
                ['name' => 'ゲーム', 'key' => 'game'],
                ['name' => '旅行', 'key' => 'travel'],
                ['name' => '仕事', 'key' => 'work'],
                ['name' => '学校', 'key' => 'school'],
                ['name' => 'グルメ', 'key' => 'gourmet'],
                ['name' => '趣味', 'key' => 'hobby'],
                ['name' => 'ジム', 'key' => 'gym'],
                ['name' => '飲み物', 'key' => 'drink'],
                ['name' => '生活', 'key' => 'living'],
                ['name' => 'ファッション', 'key' => 'fashion'],
                ['name' => '美容', 'key' => 'beauty'],
                ['name' => '健康', 'key' => 'health'],
                ['name' => '恋愛', 'key' => 'love'],
                ['name' => '天気', 'key' => 'weather'],
                ['name' => '文化', 'key' => 'culture'],
                ['name' => '政治', 'key' => 'politics'],
                ['name' => '経済', 'key' => 'economy'],
                ['name' => '教育', 'key' => 'education'],
                ['name' => '医療', 'key' => 'medical'],
            )
            ->create();
    }
}
