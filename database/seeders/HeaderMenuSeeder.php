<?php

namespace Database\Seeders;

use App\Models\HeaderMenuItem;
use Illuminate\Database\Seeder;

class HeaderMenuSeeder extends Seeder
{
    /**
     * Seed the default header navigation from the original static menu.
     */
    public function run(): void
    {
        if (HeaderMenuItem::query()->exists()) {
            return;
        }

        $menu = [
            [
                'label' => 'Beranda',
                'url' => '/#beranda',
                'children' => [],
            ],
            [
                'label' => 'Profil',
                'url' => '/profil',
                'children' => [
                    ['label' => 'Selayang Pandang', 'url' => '/profil'],
                    ['label' => 'Statistik Desa', 'url' => '/profil#statistik-desa'],
                    ['label' => 'Keuangan Desa', 'url' => '/#infografis-keuangan'],
                ],
            ],
            [
                'label' => 'Pemerintahan',
                'url' => '/pemerintahan',
                'children' => [
                    ['label' => 'Perangkat Desa', 'url' => '/pemerintahan'],
                    ['label' => 'Agenda Desa', 'url' => '/#agenda'],
                    ['label' => 'APBDes', 'url' => '/#infografis-keuangan'],
                ],
            ],
            [
                'label' => 'Layanan',
                'url' => '/layanan',
                'children' => [],
            ],
            [
                'label' => 'Berita',
                'url' => '/berita',
                'children' => [],
            ],
            [
                'label' => 'Agenda',
                'url' => '/#agenda',
                'children' => [],
            ],
            [
                'label' => 'Galeri',
                'url' => '/galeri/foto',
                'children' => [
                    ['label' => 'Galeri Foto', 'url' => '/galeri/foto'],
                    ['label' => 'Galeri Video', 'url' => '/galeri/video'],
                ],
            ],
            [
                'label' => 'Kontak',
                'url' => '/kontak',
                'children' => [],
            ],
        ];

        foreach ($menu as $position => $item) {
            $parent = HeaderMenuItem::query()->updateOrCreate(
                [
                    'parent_id' => null,
                    'label' => $item['label'],
                ],
                [
                    'url' => $item['url'],
                    'open_in_new_tab' => false,
                    'order' => $position + 1,
                    'is_active' => true,
                ],
            );

            foreach ($item['children'] as $childPosition => $child) {
                HeaderMenuItem::query()->updateOrCreate(
                    [
                        'parent_id' => $parent->getKey(),
                        'label' => $child['label'],
                    ],
                    [
                        'url' => $child['url'],
                        'open_in_new_tab' => false,
                        'order' => $childPosition + 1,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
