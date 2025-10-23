<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cause;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CauseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cause::factory()->count(8)->create();
    }
}
