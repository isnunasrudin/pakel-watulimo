<?php

namespace App\Filament\Resources\Videos\Tables;

use App\Models\Video;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->state(fn (Video $record): string => $record->thumbnailUrl())
                    ->imageSize(96)
                    ->square()
                    ->alt(fn (Video $record): string => $record->title),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('video_url')
                    ->label('URL')
                    ->limit(50)
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (Video $record): string => $record->isPublished() ? 'Terbit' : 'Draft')
                    ->color(fn (Video $record): string => $record->isPublished() ? 'success' : 'gray'),
                TextColumn::make('published_at')
                    ->label('Tanggal terbit')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Belum terbit'),
            ])
            ->recordActions([
                Action::make('openVideo')
                    ->label('Buka video')
                    ->url(fn (Video $record): string => $record->video_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Video $record): bool => filled($record->video_url)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
