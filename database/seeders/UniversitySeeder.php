<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        University::create(['name' => 'جامعة الرشيد']);
        University::create(['name' => 'جامعة اليرموك']);
        University::create(['name' => 'iust']);
        University::create(['name' => 'aiu']);

        Media::created(['name' => 'instagram', 'url' => 'd']);
        Media::created(['name' => 'telegram', 'url' => 'd']);
        Media::created(['name' => 'whatsapp', 'url' => 'd']);
    }
}
