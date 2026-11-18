<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Seed the demo albums and photos that were previously hardcoded in the gallery page.
     */
    public function run(): void
    {
        foreach ($this->albums() as $order => $albumData) {
            $album = Album::updateOrCreate(
                ['slug' => $albumData['slug']],
                [
                    'title' => $albumData['title'],
                    'description' => $albumData['description'],
                    'is_published' => true,
                    'order' => $order + 1,
                ],
            );

            foreach ($albumData['photos'] as $photoOrder => $photoData) {
                Photo::updateOrCreate(
                    [
                        'album_id' => $album->id,
                        'order' => $photoOrder + 1,
                    ],
                    [
                        'path' => 'https://picsum.photos/seed/'.$photoData['seed'].'/1200/900',
                        'caption' => $photoData['caption'],
                        'taken_at' => $photoData['taken_at'],
                    ],
                );
            }
        }
    }

    /**
     * Get the demo album records.
     *
     * @return list<array{
     *     title: string,
     *     slug: string,
     *     description: string,
     *     photos: list<array{seed: string, caption: string, taken_at: string}>
     * }>
     */
    private function albums(): array
    {
        return [
            [
                'title' => 'Kegiatan Warga',
                'slug' => 'kegiatan-warga',
                'description' => 'Kebersamaan dan aktivitas masyarakat Desa Pakel.',
                'photos' => [
                    ['seed' => 'g1', 'caption' => 'Kegiatan warga Desa Pakel', 'taken_at' => '2026-11-05 00:00:00'],
                    ['seed' => 'g7', 'caption' => 'Suasana pasar desa', 'taken_at' => '2026-10-18 00:00:00'],
                    ['seed' => 'g8', 'caption' => 'Gotong royong membersihkan lingkungan', 'taken_at' => '2026-10-15 00:00:00'],
                ],
            ],
            [
                'title' => 'Pembangunan Desa',
                'slug' => 'pembangunan-desa',
                'description' => 'Dokumentasi pembangunan dan perawatan infrastruktur desa.',
                'photos' => [
                    ['seed' => 'g2', 'caption' => 'Pembangunan infrastruktur desa', 'taken_at' => '2026-11-02 00:00:00'],
                    ['seed' => 'g9', 'caption' => 'Peresmian jalan penghubung antar dusun', 'taken_at' => '2026-10-10 00:00:00'],
                    ['seed' => 'g10', 'caption' => 'Kegiatan pertanian warga Desa Pakel', 'taken_at' => '2026-10-08 00:00:00'],
                ],
            ],
            [
                'title' => 'Posyandu & Kesehatan',
                'slug' => 'posyandu-kesehatan',
                'description' => 'Kegiatan pelayanan kesehatan dan kebersamaan warga.',
                'photos' => [
                    ['seed' => 'g3', 'caption' => 'Kegiatan Posyandu Desa Pakel', 'taken_at' => '2026-11-01 00:00:00'],
                    ['seed' => 'p1', 'caption' => 'Pelayanan kesehatan untuk warga', 'taken_at' => '2026-10-22 00:00:00'],
                    ['seed' => 'p2', 'caption' => 'Pemeriksaan kesehatan masyarakat', 'taken_at' => '2026-10-12 00:00:00'],
                ],
            ],
            [
                'title' => 'Seni & Budaya',
                'slug' => 'seni-budaya',
                'description' => 'Kesenian, tradisi, dan perayaan budaya Desa Pakel.',
                'photos' => [
                    ['seed' => 'g4', 'caption' => 'Seni dan budaya masyarakat Desa Pakel', 'taken_at' => '2026-10-28 00:00:00'],
                    ['seed' => 'g6', 'caption' => 'Festival Desa Pakel', 'taken_at' => '2026-10-20 00:00:00'],
                    ['seed' => 'c1', 'caption' => 'Pertunjukan seni warga', 'taken_at' => '2026-10-05 00:00:00'],
                ],
            ],
        ];
    }
}
