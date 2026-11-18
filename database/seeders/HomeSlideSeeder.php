<?php

namespace Database\Seeders;

use App\Models\HomeSlide;
use Illuminate\Database\Seeder;

class HomeSlideSeeder extends Seeder
{
    /**
     * Seed the default homepage slides from the original static design.
     */
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Keindahan Alam Desa Pakel',
                'description' => 'Sawah & perbukitan Watulimo.',
                'image' => 'https://picsum.photos/seed/sp1/900/420',
                'order' => 1,
            ],
            [
                'title' => 'Aktivitas Masyarakat',
                'description' => 'Gotong royong warga.',
                'image' => 'https://picsum.photos/seed/sp2/900/420',
                'order' => 2,
            ],
            [
                'title' => 'Fasilitas Desa',
                'description' => 'Balai desa & infrastruktur.',
                'image' => 'https://picsum.photos/seed/sp3/900/420',
                'order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            HomeSlide::updateOrCreate(
                ['title' => $slide['title']],
                [...$slide, 'source_type' => HomeSlide::SOURCE_MANUAL, 'is_active' => true],
            );
        }
    }
}
