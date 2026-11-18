<?php

namespace App\Filament\Resources\ServiceLinks\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\ServiceLink;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceLinksTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                TextColumn::make('title')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(70)
                    ->wrap(),
                TextColumn::make('url')
                    ->label('Tautan')
                    ->formatStateUsing(fn (ServiceLink $record): string => $record->safeUrl() ?? 'URL tidak valid')
                    ->limit(60),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (ServiceLink $record): Builder => ServiceLink::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
