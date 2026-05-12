<?php

namespace Database\Seeders;

use App\Models\BusCategory;
use App\Models\Buses;
use App\Models\BusFacilities;
use App\Models\BusImage;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $categories = BusCategory::all();
        $facilities = BusFacilities::all();

        // Kumpulan link gambar bus berkualitas (Unsplash)
        $busImages = [
            'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1557223562-6c77ff16210f?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1562620644-66ba47685044?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1518214214534-11910565863d?q=80&w=1000&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1542732056-420422502660?q=80&w=1000&auto=format&fit=crop',
        ];

        if ($categories->isEmpty()) {
            return;
        }

        foreach ($categories as $category) {
            Buses::factory()->count(3)->create(['bus_category_id' => $category->id])
                ->each(function ($bus) use ($facilities, $busImages) {
                    $bus->facilities()->attach($facilities->random(rand(3, 6))->pluck('id')->toArray());

                    // Ambil 3-4 gambar unik dari list untuk setiap bus
                    $selectedImages = collect($busImages)->random(rand(3, 4));
                    foreach ($selectedImages->values() as $index => $imageUrl) {
                        BusImage::create([
                            'bus_id' => $bus->id,
                            'image_path' => $imageUrl,
                            'caption' => "Bus {$bus->name} - Image ".($index + 1),
                            'is_primary' => $index === 0,
                            'sort_order' => $index,
                        ]);
                    }
                });
        }
    }
}
