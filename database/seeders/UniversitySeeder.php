<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\university;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        university::create(['name' => 'جامعة الرشيد']);
        university::create(['name' => 'جامعة اليرموك']);
        university::create(['name' => 'iust']);
        university::create(['name' => 'aiu']);

        Media::created(['name' => 'instagram', 'url' => 'd']);
        Media::created(['name' => 'telegram', 'url' => 'd']);
        Media::created(['name' => 'whatsapp', 'url' => 'd']);
    }
}
