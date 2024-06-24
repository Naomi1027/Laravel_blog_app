<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()
            ->count(20)
            ->sequence(fn (Sequence $sequence) => ['name' => 'tag-'.$sequence->index + 1])
            ->sequence(fn (Sequence $sequence) => ['key' => 'key-'.$sequence->index + 1])
            ->create();
    }
}
