<?php

namespace App\Filament\Resources\HomeSlides\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\HomeSlide;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HomeSlidesTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'media',
                'photo.media',
                'photo.album',
                'article.media',
            ]))
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->state(fn (HomeSlide $record): string => $record->imageUrl())
                    ->imageSize(96)
                    ->square()
                    ->alt(fn (HomeSlide $record): string => $record->resolvedTitle()),
                TextColumn::make('source_type')
                    ->label('Sumber')
                    ->badge()
                    ->formatStateUsing(fn (HomeSlide $record): string => $record->sourceLabel())
                    ->color(fn (HomeSlide $record): string => match ($record->source_type) {
                        HomeSlide::SOURCE_GALLERY => 'info',
                        HomeSlide::SOURCE_ARTICLE => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('title')
                    ->label('Judul')
                    ->state(fn (HomeSlide $record): string => $record->resolvedTitle())
                    ->wrap(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->state(fn (HomeSlide $record): ?string => $record->resolvedDescription())
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—'),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('source_type')
                    ->label('Sumber')
                    ->options([
                        HomeSlide::SOURCE_MANUAL => 'Upload manual',
                        HomeSlide::SOURCE_GALLERY => 'Galeri foto',
                        HomeSlide::SOURCE_ARTICLE => 'Artikel',
                    ]),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (HomeSlide $record): Builder => HomeSlide::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
