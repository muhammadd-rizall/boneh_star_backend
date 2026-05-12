<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Wisata', 'Ziarah', 'Study Tour', 'Antar Jemput', 'Event'];

        foreach ($types as $type) {
            ActivityType::updateOrCreate(['name' => $type]);
        }
    }
}
