<?php

namespace App\Filament\Resources\ProgramLinks\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\ProgramLink;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramLinksTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('media'))
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->state(fn (ProgramLink $record): ?string => $record->imageUrl())
                    ->imageSize(96)
                    ->square()
                    ->extraImgAttributes(fn (ProgramLink $record): array => [
                        'style' => 'background-color: '.$record->backgroundColor().';',
                    ]),
                TextColumn::make('title')
                    ->label('Nama sinergi program (internal)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label('Tautan')
                    ->formatStateUsing(fn (ProgramLink $record): string => $record->safeUrl() ?? 'URL tidak valid')
                    ->limit(60),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (ProgramLink $record): Builder => ProgramLink::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
