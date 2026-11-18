<?php

namespace App\Filament\Resources\VillageSettings\Schemas;

use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Rules\SafeUrl;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VillageSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas visual')
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('icon')
                            ->label('Ikon / logo')
                            ->collection('icon')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->imageEditor()
                            ->helperText('JPEG, PNG, WebP, atau SVG. Maksimal 2 MB. Logo raster otomatis dibuatkan versi WebP; SVG tetap dipertahankan.')
                            ->columnSpan(1),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->label('Favicon')
                            ->collection('favicon')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->maxSize(512)
                            ->helperText('PNG, SVG, WebP, atau ICO. Maksimal 512 KB. Favicon raster dibuatkan versi WebP; SVG/ICO tetap dipertahankan.')
                            ->columnSpan(1),
                        WebpMediaLibraryFileUpload::make('hero')
                            ->label('Gambar hero beranda')
                            ->collection('hero')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->webpMaxWidth(1920)
                            ->imageEditor()
                            ->helperText('JPEG, PNG, atau WebP. Maksimal 4 MB. Otomatis dioptimalkan dan dikonversi ke WebP. Kosongkan untuk memakai cover album pertama.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Profil desa')
                    ->columns(2)
                    ->schema([
                        TextInput::make('village_name')
                            ->label('Nama desa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('district_name')
                            ->label('Kecamatan')
                            ->maxLength(255),
                        TextInput::make('regency_name')
                            ->label('Kabupaten')
                            ->maxLength(255),
                        TextInput::make('province_name')
                            ->label('Provinsi')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Deskripsi profil')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Section::make('Copywriting hero beranda')
                    ->description('Kosongkan kolom untuk memakai teks bawaan. Deskripsi memakai deskripsi profil bila kolom ini kosong.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_badge')
                            ->label('Teks badge')
                            ->maxLength(255)
                            ->placeholder('Selamat Datang di Website Resmi'),
                        TextInput::make('hero_title')
                            ->label('Judul utama')
                            ->maxLength(255)
                            ->placeholder('Nama desa')
                            ->helperText('Kosongkan untuk memakai nama desa.'),
                        TextInput::make('hero_highlight')
                            ->label('Baris penekanan')
                            ->maxLength(255)
                            ->placeholder('Kecamatan Watulimo')
                            ->helperText('Tampil di bawah judul dengan warna kuning.'),
                        Textarea::make('hero_description')
                            ->label('Deskripsi singkat')
                            ->rows(3)
                            ->maxLength(600)
                            ->columnSpanFull()
                            ->helperText('Kosongkan untuk memakai deskripsi profil desa.'),
                        TextInput::make('hero_primary_label')
                            ->label('Label tombol utama')
                            ->maxLength(100)
                            ->placeholder('Berita Terbaru'),
                        TextInput::make('hero_primary_url')
                            ->label('Tautan tombol utama')
                            ->maxLength(2048)
                            ->placeholder('#berita')
                            ->helperText('Boleh berupa anchor (#berita), path internal (/berita), atau URL lengkap.')
                            ->rules([
                                new SafeUrl(['http', 'https'], message: 'Masukkan tautan HTTP(S), path internal, atau anchor yang valid.'),
                            ]),
                        TextInput::make('hero_secondary_label')
                            ->label('Label tombol kedua')
                            ->maxLength(100)
                            ->placeholder('Profil Desa'),
                        TextInput::make('hero_secondary_url')
                            ->label('Tautan tombol kedua')
                            ->maxLength(2048)
                            ->placeholder('#profil')
                            ->helperText('Boleh berupa anchor (#profil), path internal (/profil), atau URL lengkap.')
                            ->rules([
                                new SafeUrl(['http', 'https'], message: 'Masukkan tautan HTTP(S), path internal, atau anchor yang valid.'),
                            ]),
                    ]),
                Section::make('Judul bagian beranda')
                    ->description('Kosongkan kolom untuk memakai judul bawaan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('home_news_title')
                            ->label('Judul bagian berita')
                            ->maxLength(255)
                            ->placeholder('Berita & Artikel Terbaru'),
                        TextInput::make('home_news_link_label')
                            ->label('Label tautan berita')
                            ->maxLength(100)
                            ->placeholder('Lihat Semua'),
                        TextInput::make('home_finance_title')
                            ->label('Judul bagian APBDes')
                            ->maxLength(255)
                            ->placeholder('Infografis Keuangan Desa'),
                        TextInput::make('home_statistics_title')
                            ->label('Judul bagian statistik')
                            ->maxLength(255)
                            ->placeholder('Statistik Desa'),
                    ]),
                Section::make('Alamat dan kontak')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat kantor desa')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(40),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('whatsapp')
                            ->label('Tautan WhatsApp')
                            ->maxLength(2048)
                            ->rules([
                                new SafeUrl(['https'], absolute: true, message: 'Masukkan URL WhatsApp dengan skema HTTPS.'),
                            ]),
                        TextInput::make('office_hours')
                            ->label('Jam pelayanan')
                            ->maxLength(255),
                    ]),
                Section::make('Peta')
                    ->columns(2)
                    ->schema([
                        TextInput::make('maps_url')
                            ->label('Tautan Google Maps')
                            ->maxLength(2048)
                            ->rules([
                                new SafeUrl(['http', 'https'], absolute: true, message: 'Masukkan URL peta HTTP(S) yang valid.'),
                            ]),
                        TextInput::make('map_embed_url')
                            ->label('URL embed peta')
                            ->maxLength(2048)
                            ->helperText('Masukkan URL embed HTTPS, bukan kode HTML iframe.')
                            ->rules([
                                new SafeUrl(['https'], absolute: true, message: 'URL embed peta harus menggunakan HTTPS.'),
                            ]),
                    ]),
            ]);
    }
}
