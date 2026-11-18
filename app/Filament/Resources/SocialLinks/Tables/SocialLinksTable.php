<?php

namespace App\Filament\Resources\SocialLinks\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\SocialLink;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SocialLinksTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                TextColumn::make('platform')
                    ->label('Platform')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->formatStateUsing(fn (SocialLink $record): string => $record->safeUrl() ?? 'URL tidak valid')
                    ->limit(60),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                ...static::recordOrderActions(fn (SocialLink $record): Builder => SocialLink::query()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
