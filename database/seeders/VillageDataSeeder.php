<?php

namespace Database\Seeders;

use App\Models\ProgramLink;
use App\Models\ServiceLink;
use App\Models\VillageOfficial;
use App\Models\VillageSetting;
use App\Models\VillageStatistic;
use Illuminate\Database\Seeder;

class VillageDataSeeder extends Seeder
{
    /**
     * Seed demo village profile data that was previously hardcoded in the views.
     */
    public function run(): void
    {
        VillageSetting::query()->firstOrCreate([], [
            'village_name' => 'Desa Pakel',
            'district_name' => 'Watulimo',
            'regency_name' => 'Trenggalek',
            'province_name' => 'Jawa Timur',
            'description' => 'Desa Pakel berada di Kecamatan Watulimo, Kabupaten Trenggalek, Provinsi Jawa Timur.',
            'address' => 'Balai Desa Pakel, Kecamatan Watulimo, Kabupaten Trenggalek, Jawa Timur.',
            'office_hours' => 'Senin–Jumat, 08.00–15.00 WIB',
        ]);

        $officials = [
            ['name' => 'Sutrisno, S.Sos', 'position' => 'Kepala Desa', 'order' => 1],
            ['name' => 'Bambang Irawan', 'position' => 'Sekretaris Desa', 'order' => 2],
            ['name' => 'Sri Wahyuni', 'position' => 'Bendahara Desa', 'order' => 3],
            ['name' => 'Agus Santoso', 'position' => 'Kasi Pemerintahan', 'order' => 4],
            ['name' => 'Dewi Lestari', 'position' => 'Kasi Kesejahteraan', 'order' => 5],
            ['name' => 'Rudi Hartono', 'position' => 'Kasi Pelayanan', 'order' => 6],
        ];

        foreach ($officials as $official) {
            VillageOfficial::updateOrCreate(
                ['name' => $official['name']],
                $official,
            );
        }

        $statistics = [
            ['label' => 'Jiwa Penduduk', 'value' => '3.248', 'unit' => 'jiwa', 'icon' => 'fas fa-users', 'group' => 'demografi', 'order' => 1],
            ['label' => 'Kepala Keluarga', 'value' => '912', 'unit' => 'KK', 'icon' => 'fas fa-home', 'group' => 'demografi', 'order' => 2],
            ['label' => 'Laki-laki', 'value' => '1.642', 'unit' => 'orang', 'icon' => 'fas fa-mars', 'group' => 'demografi', 'order' => 3],
            ['label' => 'Perempuan', 'value' => '1.606', 'unit' => 'orang', 'icon' => 'fas fa-venus', 'group' => 'demografi', 'order' => 4],
            ['label' => 'Dusun', 'value' => '8', 'unit' => 'dusun', 'icon' => 'fas fa-map-marker-alt', 'group' => 'wilayah', 'order' => 5],
            ['label' => 'Luas Wilayah', 'value' => '412', 'unit' => 'Ha', 'icon' => 'fas fa-map', 'group' => 'wilayah', 'order' => 6],
        ];

        foreach ($statistics as $statistic) {
            VillageStatistic::updateOrCreate(
                ['label' => $statistic['label']],
                $statistic,
            );
        }

        $programs = [
            ['title' => 'Ketahanan Pangan', 'description' => 'Produksi pangan lokal.', 'icon' => 'fas fa-seedling', 'url' => '/layanan', 'order' => 1],
            ['title' => 'UMKM & Ekonomi Kreatif', 'description' => 'Pendampingan UMKM.', 'icon' => 'fas fa-store', 'url' => '/layanan', 'order' => 2],
            ['title' => 'Kesehatan Masyarakat', 'description' => 'Sinergi layanan kesehatan.', 'icon' => 'fas fa-heartbeat', 'url' => '/layanan', 'order' => 3],
            ['title' => 'Pendidikan & Literasi', 'description' => 'Dukungan pendidikan masyarakat.', 'icon' => 'fas fa-graduation-cap', 'url' => '/layanan', 'order' => 4],
        ];

        foreach ($programs as $program) {
            ProgramLink::updateOrCreate(
                ['title' => $program['title']],
                $program,
            );
        }

        $services = [
            ['title' => 'Profil Desa', 'description' => 'Informasi umum Desa Pakel.', 'icon' => 'fas fa-id-card', 'url' => '/profil', 'order' => 1],
            ['title' => 'Perangkat Desa', 'description' => 'Susunan pemerintahan desa.', 'icon' => 'fas fa-building', 'url' => '/pemerintahan', 'order' => 2],
            ['title' => 'Layanan Masyarakat', 'description' => 'Informasi layanan dan tautan desa.', 'icon' => 'fas fa-file-alt', 'url' => '/layanan', 'order' => 3],
            ['title' => 'Berita Desa', 'description' => 'Kabar terbaru dari Desa Pakel.', 'icon' => 'fas fa-link', 'url' => '/berita', 'order' => 4],
            ['title' => 'Galeri Foto', 'description' => 'Dokumentasi kegiatan Desa Pakel.', 'icon' => 'fas fa-map-marker-alt', 'url' => '/galeri/foto', 'order' => 5],
        ];

        foreach ($services as $service) {
            ServiceLink::updateOrCreate(
                ['title' => $service['title']],
                $service,
            );
        }
    }
}
