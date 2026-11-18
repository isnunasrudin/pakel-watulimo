<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Seed the demo content that was previously hardcoded in Blade views.
     */
    public function run(): void
    {
        $author = $this->author();

        $categories = collect([
            ['name' => 'Pembangunan', 'slug' => 'pembangunan', 'color' => 'green', 'order' => 1, 'description' => 'Infrastruktur dan pembangunan desa.'],
            ['name' => 'Pemerintahan', 'slug' => 'pemerintahan', 'color' => 'amber', 'order' => 2, 'description' => 'Kegiatan pemerintahan dan layanan publik.'],
            ['name' => 'Sosial', 'slug' => 'sosial', 'color' => 'yellow', 'order' => 3, 'description' => 'Bantuan sosial dan kegiatan warga.'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'color' => 'blue', 'order' => 4, 'description' => 'Pendidikan dan pelatihan masyarakat.'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'color' => 'purple', 'order' => 5, 'description' => 'Posyandu dan layanan kesehatan.'],
            ['name' => 'Lingkungan', 'slug' => 'lingkungan', 'color' => 'teal', 'order' => 6, 'description' => 'Penghijauan dan kebersihan lingkungan.'],
            ['name' => 'Kebencanaan', 'slug' => 'kebencanaan', 'color' => 'red', 'order' => 7, 'description' => 'Kesiapsiagaan dan penanggulangan bencana.'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'color' => 'indigo', 'order' => 8, 'description' => 'Digitalisasi dan layanan berbasis internet.'],
            ['name' => 'Budaya', 'slug' => 'budaya', 'color' => 'pink', 'order' => 9, 'description' => 'Seni, tradisi, dan kegiatan budaya desa.'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        foreach ($this->articles() as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'user_id' => $author->id,
                    'category_id' => $categories[$article['category']]->id,
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'is_pinned' => $article['is_pinned'] ?? false,
                    'is_featured' => $article['is_featured'] ?? false,
                    'status' => 'published',
                    'published_at' => $article['published_at'],
                    'views_count' => $article['views'],
                    'meta_title' => $article['title'].' | Desa Pakel',
                    'meta_description' => $article['excerpt'],
                ],
            );
        }

        Announcement::updateOrCreate(
            ['title' => 'Pengumuman Penting Desa Pakel'],
            [
                'body' => 'Pembayaran PBB dan retribusi pasar dilaksanakan setiap Senin-Kamis di Balai Desa. Bawa dokumen pendukung.',
                'level' => 'info',
                'link_label' => null,
                'link_url' => null,
                'starts_at' => now()->subWeek(),
                'ends_at' => null,
                'is_active' => true,
            ],
        );

        $featured = Article::query()->where('slug', 'peresmian-jalan-desa-pakel-watulimo-tahap-ii')->first();

        if ($featured instanceof Article) {
            ArticleComment::firstOrCreate(
                ['article_id' => $featured->id, 'name' => 'Siti Rahayu'],
                [
                    'body' => 'Alhamdulillah, jalan antar dusun sekarang jauh lebih nyaman dilalui.',
                    'status' => ArticleComment::STATUS_APPROVED,
                    'ip_address' => '127.0.0.1',
                ],
            );

            ArticleComment::firstOrCreate(
                ['article_id' => $featured->id, 'name' => 'Budi Santoso'],
                [
                    'body' => 'Terima kasih pemerintah desa. Semoga perawatannya juga diperhatikan.',
                    'status' => ArticleComment::STATUS_APPROVED,
                    'ip_address' => '127.0.0.1',
                ],
            );

            ArticleComment::firstOrCreate(
                ['article_id' => $featured->id, 'name' => 'Warga Krajan'],
                [
                    'body' => 'Mohon informasi jadwal perbaikan saluran air di sekitar jalan baru.',
                    'status' => ArticleComment::STATUS_PENDING,
                    'ip_address' => '127.0.0.1',
                ],
            );
        }
    }

    /**
     * Get the author used for demo articles.
     */
    private function author(): User
    {
        return User::query()->where('is_admin', true)->first()
            ?? User::factory()->admin()->create([
                'name' => 'Administrator Desa Pakel',
                'email' => 'admin@desapakel.id',
            ]);
    }

    /**
     * Get the demo articles.
     *
     * @return list<array{
     *     slug: string,
     *     title: string,
     *     category: string,
     *     excerpt: string,
     *     body: string,
     *     published_at: string,
     *     views: int,
     *     is_pinned?: bool,
     *     is_featured?: bool
     * }>
     */
    private function articles(): array
    {
        return [
            [
                'slug' => 'peresmian-jalan-desa-pakel-watulimo-tahap-ii',
                'title' => 'Peresmian Jalan Desa Pakel - Watulimo Tahap II Selesai Dibangun',
                'category' => 'pembangunan',
                'excerpt' => 'Pemerintah Desa Pakel meresmikan jalan penghubung antar dusun yang baru saja selesai dibangun dengan anggaran DD tahun 2026.',
                'body' => '<p>Pemerintah Desa Pakel bersama warga meresmikan jalan penghubung antar dusun pada tahap kedua. Pembangunan menggunakan anggaran Dana Desa tahun 2026 dan dikerjakan secara bergotong royong.</p><h2>Manfaat bagi warga</h2><p>Jalan sepanjang 1,8 kilometer ini menghubungkan Dusun Krajan dengan Dusun Ngemplak. Warga kini dapat mengangkut hasil pertanian lebih cepat dan biaya transportasi menurun.</p><p>Perangkat desa mengingatkan bahwa perawatan jalan menjadi tanggung jawab bersama. Kerusakan ringan dapat dilaporkan langsung ke kantor desa atau melalui perangkat dusun.</p>',
                'published_at' => '2026-11-05 08:00:00',
                'views' => 342,
                'is_pinned' => true,
                'is_featured' => true,
            ],
            [
                'slug' => 'bantuan-langsung-tunai-disalurkan-120-kpm',
                'title' => 'Bantuan Langsung Tunai (BLT) Disalurkan kepada 120 KPM',
                'category' => 'sosial',
                'excerpt' => 'Penyaluran BLT Dana Desa tahap 3 telah dilaksanakan di balai desa dengan tetap menerapkan protokol kesehatan.',
                'body' => '<p>Sebanyak 120 Keluarga Penerima Manfaat (KPM) menerima Bantuan Langsung Tunai Dana Desa tahap ketiga. Penyaluran dilakukan di Balai Desa Pakel dengan pengaturan antrean agar tetap tertib.</p><p>Penerima diimbau membawa KTP dan kartu keluarga asli. Bagi warga yang berhalangan hadir, bantuan dapat diambil oleh anggota keluarga dengan surat kuasa.</p>',
                'published_at' => '2026-11-03 09:30:00',
                'views' => 218,
            ],
            [
                'slug' => 'pelatihan-keterampilan-digital-pemuda-desa-pakel',
                'title' => 'Pelatihan Keterampilan Digital untuk Pemuda Desa Pakel',
                'category' => 'pendidikan',
                'excerpt' => 'Karang Taruna Desa Pakel mengadakan pelatihan dasar penggunaan komputer dan internet bagi remaja.',
                'body' => '<p>Karang Taruna Desa Pakel mengadakan pelatihan keterampilan digital selama dua hari di balai desa. Materi meliputi pengelolaan dokumen, pemasaran daring, dan keamanan data pribadi.</p><p>Pelatihan diikuti 35 peserta dari berbagai dusun. Pemerintah desa berencana menjadwalkan kelas lanjutan setiap triwulan.</p>',
                'published_at' => '2026-11-01 10:00:00',
                'views' => 156,
            ],
            [
                'slug' => 'posyandu-balita-november-imunisasi-penimbangan',
                'title' => 'Posyandu Balita Bulan November: Imunisasi & Penimbangan',
                'category' => 'kesehatan',
                'excerpt' => 'Seluruh posyandu di Desa Pakel melaksanakan kegiatan penimbangan dan imunisasi rutin bulanan.',
                'body' => '<p>Seluruh posyandu di Desa Pakel melaksanakan penimbangan balita dan imunisasi rutin pada awal November. Kegiatan didampingi bidan desa dan kader posyandu.</p><p>Orang tua diimbau membawa buku KIA serta memastikan jadwal imunisasi anak tidak terlewat. Konsultasi gizi juga tersedia tanpa biaya.</p>',
                'published_at' => '2026-10-30 08:00:00',
                'views' => 198,
            ],
            [
                'slug' => 'penanaman-500-pohon-jalan-desa-pakel',
                'title' => 'Penanaman 500 Pohon di Sepanjang Jalan Desa Pakel',
                'category' => 'lingkungan',
                'excerpt' => 'Warga bersama karang taruna menanam pohon pelindung untuk penghijauan dan pencegahan erosi.',
                'body' => '<p>Warga bersama Karang Taruna menanam 500 bibit pohon di sepanjang jalan desa dan area mata air. Jenis yang ditanam meliputi mahoni, trembesi, dan buah-buahan lokal.</p><p>Setiap dusun bertanggung jawab merawat bibit di wilayahnya. Penyiraman dilakukan bergiliran selama musim kemarau.</p>',
                'published_at' => '2026-10-29 07:30:00',
                'views' => 134,
            ],
            [
                'slug' => 'simulasi-tanggap-darurat-bencana-longsor-dusun-krajan',
                'title' => 'Simulasi Tanggap Darurat Bencana Longsor di Dusun Krajan',
                'category' => 'kebencanaan',
                'excerpt' => 'BPBD dan perangkat desa mengadakan simulasi evakuasi untuk meningkatkan kesiapsiagaan warga.',
                'body' => '<p>BPBD Kabupaten Trenggalek bersama perangkat desa mengadakan simulasi evakuasi bencana longsor di Dusun Krajan. Warga dilatih mengenali tanda bahaya dan mengikuti jalur evakuasi.</p><p>Titik kumpul sementara disiapkan di lapangan desa. Peta jalur evakuasi akan dipasang di setiap dusun.</p>',
                'published_at' => '2026-10-27 13:00:00',
                'views' => 201,
            ],
            [
                'slug' => 'pemasangan-wifi-gratis-balai-desa-area-publik',
                'title' => 'Pemasangan WiFi Gratis di Balai Desa dan Area Publik',
                'category' => 'teknologi',
                'excerpt' => 'Pemerintah desa menyediakan akses internet gratis untuk mendukung layanan dan pendidikan.',
                'body' => '<p>Pemerintah desa memasang akses internet gratis di balai desa, perpustakaan, dan area publik. Fasilitas ini ditujukan untuk mendukung layanan administrasi dan belajar warga.</p><p>Penggunaan dibatasi untuk keperluan produktif pada jam layanan. Kata sandi jaringan dapat ditanyakan di kantor desa.</p>',
                'published_at' => '2026-10-26 11:00:00',
                'views' => 275,
            ],
            [
                'slug' => 'festival-seni-tradisional-desa-pakel',
                'title' => 'Festival Seni Tradisional Desa Pakel Meriahkan Akhir Pekan',
                'category' => 'budaya',
                'excerpt' => 'Berbagai kesenian lokal ditampilkan oleh sanggar seni dan pelajar dalam festival tahunan.',
                'body' => '<p>Festival seni tradisional Desa Pakel menampilkan tari, karawitan, dan musik tradisional dari sanggar seni serta pelajar. Acara digelar di lapangan desa selama dua hari.</p><p>Festival menjadi ajang pelestarian budaya sekaligus promosi produk UMKM desa.</p>',
                'published_at' => '2026-10-24 16:00:00',
                'views' => 312,
            ],
        ];
    }
}
