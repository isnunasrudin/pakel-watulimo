<?php

namespace App\Filament\Resources\VillageOfficials\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\VillageOfficial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VillageOfficialsTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->state(fn (VillageOfficial $record): string => $record->photoUrl())
                    ->disk('public')
                    ->imageSize(64)
                    ->circular()
                    ->extraImgAttributes(fn (VillageOfficial $record): array => [
                        'style' => 'background-color: '.$record->backgroundColor().';',
                    ])
                    ->alt(fn (VillageOfficial $record): string => $record->name),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('—'),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (VillageOfficial $record): Builder => VillageOfficial::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
