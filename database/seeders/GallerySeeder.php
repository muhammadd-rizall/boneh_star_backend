<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'title' => 'Tour Wisata Bali Mandiri 2026',
                'description' => 'Perjalanan wisata instansi Bank Mandiri cabang Surabaya menuju Bali menggunakan armada HDD Boneh Star.',
                'date' => '2026-03-15',
                'images' => [
                    'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=800',
                    'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?q=80&w=800'
                ],
                'is_publish' => true
            ],
            [
                'title' => 'Ziarah Wali Songo Rembang-Surabaya',
                'description' => 'Armada Boneh Star menemani perjalanan ibadah ziarah Wali Songo rombongan pengajian Masjid Al-Akbar.',
                'date' => '2026-04-20',
                'images' => [
                    'https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?q=80&w=800'
                ],
                'is_publish' => true
            ],
            [
                'title' => 'Study Tour SMA Negeri 1 Malang',
                'description' => 'Keseruan Study Tour siswa-siswi SMAN 1 Malang menjelajahi destinasi sejarah dan alam di Yogyakarta.',
                'date' => '2026-05-10',
                'images' => [
                    'https://images.unsplash.com/photo-1557223562-6c77ff16210f?q=80&w=800',
                    'https://images.unsplash.com/photo-1562620644-66ba47685044?q=80&w=800'
                ],
                'is_publish' => true
            ],
            [
                'title' => 'Gathering PT Petrokimia Gresik',
                'description' => 'Layanan transportasi premium Boneh Star untuk acara family gathering PT Petrokimia Gresik di Songgoriti, Batu.',
                'date' => '2026-06-02',
                'images' => [
                    'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=800'
                ],
                'is_publish' => true
            ]
        ];

        foreach ($galleries as $gallery) {
            Gallery::create([
                'title' => $gallery['title'],
                'description' => $gallery['description'],
                'date' => $gallery['date'],
                'images' => $gallery['images'],
                'is_publish' => $gallery['is_publish'],
            ]);
        }
    }
}
