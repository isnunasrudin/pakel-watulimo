<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Services\AttachmentFileTypes;
use App\Services\ImageOptimizer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Kisame76\FilamentAdvancedRichEditor\Forms\Components\AdvancedRichEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Dipakai pada URL artikel.'),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        AdvancedRichEditor::make('body')
                            ->label('Isi artikel')
                            ->required()
                            ->preset('blog')
                            ->minHeight('12rem')
                            ->maxHeight('70vh')
                            ->extraAttributes(['class' => 'article-rich-editor-resizable'])
                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->fileAttachmentsMaxSize(4096)
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('articles/content')
                            ->fileAttachmentsVisibility('public')
                            ->saveUploadedFileAttachmentUsing(static fn (TemporaryUploadedFile $file): string => app(ImageOptimizer::class)
                                ->storeUploadedFile($file, 'public', 'articles/content', 1920))
                            ->mediaLibraryDirectory('articles/content')
                            ->mediaLibraryTypes([
                                'video' => [],
                                'audio' => [],
                                'file' => [],
                            ])
                            ->imageEditor()
                            ->helperText('Gambar isi artikel otomatis dioptimalkan dan dikonversi ke WebP (maksimal 1920 px) di folder articles/content. Editor menyediakan pengaturan alt text, caption, ukuran, dan crop.')
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Dipakai pada kartu beranda, daftar berita, dan meta description.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        WebpMediaLibraryFileUpload::make('cover')
                            ->label('Gambar sampul')
                            ->collection('cover')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->maxSize(4096)
                            ->webpMaxWidth(1600)
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['16:9', '4:3', '1:1'])
                            ->conversion('card')
                            ->helperText('Gambar otomatis dioptimalkan dan dikonversi ke WebP. Klik ikon pensil untuk memotong (crop), memutar, atau mengatur posisi.')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('Lampiran file')
                            ->collection('attachments')
                            ->disk('public')
                            ->visibility('public')
                            ->multiple()
                            ->preserveFilenames()
                            ->acceptedFileTypes(AttachmentFileTypes::MIME_TYPES)
                            ->maxSize(AttachmentFileTypes::MAX_SIZE_KB)
                            ->helperText('PDF, Word, Excel, PowerPoint, TXT, atau CSV. Maksimal 10 MB per file. Tampil sebagai daftar Lampiran di bawah artikel.')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Terbit',
                            ])
                            ->required()
                            ->default('draft')
                            ->live(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal terbit')
                            ->seconds(false)
                            ->default(now())
                            ->required(fn (callable $get): bool => $get('status') === 'published'),
                        Toggle::make('is_pinned')
                            ->label('Sematkan di beranda'),
                        Toggle::make('is_featured')
                            ->label('Tandai sebagai unggulan'),
                    ]),
                Section::make('SEO')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
