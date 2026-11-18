<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgendaSeeder extends Seeder
{
    /**
     * Seed demo agendas relative to the current date.
     */
    public function run(): void
    {
        $now = now();

        $agendas = [
            [
                'title' => 'Musyawarah Desa RKPDes 2027',
                'description' => 'Penyusunan rencana kerja pemerintah desa bersama perwakilan warga.',
                'location' => 'Balai Desa Pakel',
                'starts_at' => $now->copy()->subDays(20)->setTime(9, 0),
                'ends_at' => $now->copy()->subDays(20)->setTime(12, 0),
            ],
            [
                'title' => 'Kerja Bakti Bersih Desa',
                'description' => 'Kerja bakti membersihkan saluran air dan lingkungan dusun.',
                'location' => 'Lingkungan Desa Pakel',
                'starts_at' => $now->copy()->subDays(10)->setTime(7, 0),
                'ends_at' => $now->copy()->subDays(10)->setTime(11, 0),
            ],
            [
                'title' => 'Pelatihan UMKM & Digital Marketing',
                'description' => 'Pelatihan pemasaran digital untuk pelaku UMKM desa.',
                'location' => 'Aula Desa Pakel',
                'starts_at' => $now->copy()->subDay()->setTime(8, 0),
                'ends_at' => $now->copy()->addDay()->setTime(16, 0),
            ],
            [
                'title' => 'Peringatan Hari Pahlawan',
                'description' => 'Upacara dan kegiatan mengenang jasa para pahlawan.',
                'location' => 'Lapangan Desa Pakel',
                'starts_at' => $now->copy()->addDays(3)->setTime(7, 0),
                'ends_at' => $now->copy()->addDays(3)->setTime(10, 0),
            ],
            [
                'title' => 'Musdes Penetapan APBDes 2027',
                'description' => 'Musyawarah desa untuk menetapkan anggaran tahun 2027.',
                'location' => 'Balai Desa Pakel',
                'starts_at' => $now->copy()->addDays(10)->setTime(9, 0),
                'ends_at' => $now->copy()->addDays(10)->setTime(13, 0),
            ],
            [
                'title' => 'Festival Budaya Desa Pakel',
                'description' => 'Pementasan seni dan kuliner khas desa.',
                'location' => 'Lapangan Desa Pakel',
                'starts_at' => $now->copy()->addDays(20)->setTime(8, 0),
                'ends_at' => $now->copy()->addDays(20)->setTime(17, 0),
            ],
        ];

        foreach ($agendas as $agenda) {
            Agenda::updateOrCreate(
                ['title' => $agenda['title']],
                [
                    ...$agenda,
                    'slug' => Str::slug($agenda['title']),
                    'is_published' => true,
                ],
            );
        }
    }
}
