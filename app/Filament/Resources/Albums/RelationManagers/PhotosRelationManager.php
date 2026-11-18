<?php

namespace App\Filament\Resources\Albums\RelationManagers;

use App\Filament\Forms\Components\WebpFileUpload;
use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\Album;
use App\Models\Photo;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PhotosRelationManager extends RelationManager
{
    use InteractsWithRecordOrder;

    protected static string $relationship = 'photos';

    protected static ?string $title = 'Foto dalam album';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                WebpMediaLibraryFileUpload::make('photo')
                    ->label('Foto')
                    ->collection('photo')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->webpMaxWidth(2000)
                    ->imageEditor()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('JPEG, PNG, atau WebP. Maksimal 4 MB. Otomatis dioptimalkan dan dikonversi ke WebP.')
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->label('Keterangan')
                    ->maxLength(255),
                DateTimePicker::make('taken_at')
                    ->label('Tanggal foto')
                    ->seconds(false),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('order')
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('media'))
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->state(fn (Photo $record): string => $record->thumbUrl())
                    ->imageSize(96)
                    ->square()
                    ->alt(fn (Photo $record): string => $record->caption ?: 'Foto album'),
                TextColumn::make('caption')
                    ->label('Keterangan')
                    ->searchable()
                    ->placeholder('Tanpa keterangan')
                    ->wrap(),
                TextColumn::make('cover_status')
                    ->label('Sampul')
                    ->badge()
                    ->state(fn (Photo $record, RelationManager $livewire): string => $this->isCoverPhoto($record, $livewire) ? 'Sampul aktif' : '—')
                    ->color(fn (Photo $record, RelationManager $livewire): string => $this->isCoverPhoto($record, $livewire) ? 'success' : 'gray'),
                TextColumn::make('taken_at')
                    ->label('Tanggal foto')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah satu foto'),
                Action::make('uploadMultiplePhotos')
                    ->label('Unggah beberapa foto')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        WebpFileUpload::make('photos')
                            ->label('Pilih foto')
                            ->multiple()
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->maxFiles(30)
                            ->disk('public')
                            ->directory('gallery/photos')
                            ->visibility('public')
                            ->webpMaxWidth(2000)
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        $album = $livewire->getOwnerRecord();

                        if (! $album instanceof Album) {
                            return;
                        }

                        $order = (int) $album->photos()->max('order');

                        foreach ($data['photos'] as $path) {
                            if (! is_string($path)) {
                                continue;
                            }

                            $photo = $album->photos()->create([
                                'order' => ++$order,
                            ]);

                            $photo->addMediaFromDisk($path, 'public')->toMediaCollection('photo');
                            Storage::disk('public')->delete($path);
                        }

                        $livewire->dispatch('$refresh');
                    }),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (Photo $record): Builder => Photo::query()->where('album_id', $record->album_id)),
                Action::make('setCover')
                    ->label(fn (Photo $record, RelationManager $livewire): string => $this->isCoverPhoto($record, $livewire) ? 'Sampul aktif' : 'Jadikan sampul')
                    ->icon(Heroicon::OutlinedStar)
                    ->color(fn (Photo $record, RelationManager $livewire): string => $this->isCoverPhoto($record, $livewire) ? 'success' : 'gray')
                    ->tooltip(fn (Photo $record, RelationManager $livewire): string => $this->isCoverPhoto($record, $livewire) ? 'Foto ini sudah menjadi sampul album.' : 'Jadikan foto ini sampul album.')
                    ->disabled(fn (Photo $record, RelationManager $livewire): bool => $this->isCoverPhoto($record, $livewire))
                    ->successNotificationTitle('Sampul album diperbarui')
                    ->action(function (Photo $record, RelationManager $livewire): void {
                        $album = $livewire->getOwnerRecord();

                        if (! $album instanceof Album) {
                            return;
                        }

                        $album->update(['cover_photo_id' => $record->getKey()]);
                        $livewire->getOwnerRecord()->refresh();
                        $livewire->dispatch('$refresh');
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Determine whether the photo is the current cover of the album.
     */
    private function isCoverPhoto(Photo $photo, RelationManager $livewire): bool
    {
        $album = $livewire->getOwnerRecord();

        return $album instanceof Album && $album->cover_photo_id === $photo->getKey();
    }
}
