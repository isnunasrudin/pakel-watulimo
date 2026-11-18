<?php

namespace App\Filament\Resources\Albums\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\Album;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AlbumsTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('coverPhoto.media')->withCount('photos'))
            ->columns([
                ImageColumn::make('cover_photo_id')
                    ->label('Sampul')
                    ->state(fn (Album $record): string => $record->coverThumbUrl())
                    ->imageSize(72)
                    ->square()
                    ->alt(fn (Album $record): string => $record->title),
                TextColumn::make('title')
                    ->label('Album')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('photos_count')
                    ->label('Foto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_published')
                    ->label('Terbit'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Publikasi')
                    ->boolean()
                    ->trueLabel('Album terbit')
                    ->falseLabel('Draft'),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (Album $record): Builder => Album::query()),
                Action::make('view')
                    ->label('Lihat')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Album $record): string => route('gallery.album', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Album $record): bool => $record->is_published),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
