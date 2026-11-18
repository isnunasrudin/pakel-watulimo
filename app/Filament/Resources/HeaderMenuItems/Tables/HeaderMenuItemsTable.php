<?php

namespace App\Filament\Resources\HeaderMenuItems\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\HeaderMenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HeaderMenuItemsTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('parent')
                ->withCount('children'))
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable()
                    ->description(fn (HeaderMenuItem $record): string => $record->parent_id === null
                        ? 'Menu utama'
                        : 'Submenu dari '.($record->parent?->label ?? '—')),
                TextColumn::make('url')
                    ->label('Tautan')
                    ->limit(55)
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('children_count')
                    ->label('Submenu')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('open_in_new_tab')
                    ->label('Tab baru')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->groups([
                Group::make('parent_id')
                    ->label('Posisi')
                    ->getTitleFromRecordUsing(fn (HeaderMenuItem $record): string => $record->parent?->label ?? 'Menu utama')
                    ->orderQueryUsing(fn (Builder $query): Builder => $query
                        ->orderByRaw('case when parent_id is null then 0 else 1 end')
                        ->orderBy('parent_id'))
                    ->collapsible(),
            ])
            ->defaultGroup('parent_id')
            ->recordActions([
                ...static::recordOrderActions(fn (HeaderMenuItem $record): Builder => HeaderMenuItem::query()
                    ->where('parent_id', $record->parent_id)),
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Menghapus menu induk juga akan menghapus seluruh submenunya.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Menghapus menu induk juga akan menghapus seluruh submenunya.'),
                ]),
            ]);
    }
}
