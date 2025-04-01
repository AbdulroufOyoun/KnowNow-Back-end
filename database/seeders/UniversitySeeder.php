<?php

namespace Database\Seeders;

use App\Models\university;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        university::create(['name' => 'جامعة دمشق']);
    }
}
