<?php

namespace App\Filament\Resources\BudgetItems\Tables;

use App\Filament\Resources\Concerns\InteractsWithRecordOrder;
use App\Models\BudgetItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetItemsTable
{
    use InteractsWithRecordOrder;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Builder $query): Builder => $query->orderByDesc('year')->orderBy('order')->orderBy('id'))
            ->columns([
                TextColumn::make('year')
                    ->label('Tahun')
                    ->badge()
                    ->sortable(),
                TextColumn::make('section')
                    ->label('Bagian')
                    ->badge()
                    ->formatStateUsing(fn (BudgetItem $record): string => $record->sectionLabel())
                    ->color(fn (BudgetItem $record): string => match ($record->section) {
                        BudgetItem::SECTION_PENDAPATAN => 'success',
                        BudgetItem::SECTION_BELANJA => 'warning',
                        BudgetItem::SECTION_PEMBIAYAAN => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('group')
                    ->label('Kelompok')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('label')
                    ->label('Uraian')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('budget_amount')
                    ->label('Anggaran')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('realization_amount')
                    ->label('Realisasi')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('realization_percentage')
                    ->label('Realisasi')
                    ->state(fn (BudgetItem $record): string => $record->budget_amount > 0
                        ? number_format($record->realizationPercentage(), 1, ',', '.').'%'
                        : '—')
                    ->badge()
                    ->color(fn (BudgetItem $record): string => match (true) {
                        $record->realizationPercentage() >= 100 => 'success',
                        $record->realizationPercentage() > 0 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(fn (): array => BudgetItem::query()
                        ->distinct()
                        ->orderByDesc('year')
                        ->pluck('year')
                        ->mapWithKeys(fn (int|string $year): array => [(string) $year => (string) $year])
                        ->all()),
                SelectFilter::make('section')
                    ->label('Bagian')
                    ->options([
                        BudgetItem::SECTION_PENDAPATAN => 'Pendapatan',
                        BudgetItem::SECTION_BELANJA => 'Belanja',
                        BudgetItem::SECTION_PEMBIAYAAN => 'Pembiayaan',
                    ]),
            ])
            ->groups([
                Group::make('section')
                    ->label('Bagian')
                    ->getTitleFromRecordUsing(fn (BudgetItem $record): string => $record->sectionLabel())
                    ->titlePrefixedWithLabel(false)
                    ->orderQueryUsing(fn (Builder $query): Builder => $query->orderByRaw(
                        "case when section = 'pendapatan' then 1 when section = 'belanja' then 2 when section = 'pembiayaan' then 3 else 4 end"
                    ))
                    ->collapsible(),
                Group::make('year')
                    ->label('Tahun')
                    ->collapsible(),
            ])
            ->defaultGroup('section')
            ->recordActions([
                ...static::recordOrderActions(fn (BudgetItem $record): Builder => BudgetItem::query()->where('year', $record->year)->where('section', $record->section)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
