<?php

namespace App\Filament\Resources\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

trait InteractsWithRecordOrder
{
    /**
     * Build the up/down actions for a resource that stores its order in `order`.
     *
     * @param  Closure(Model): Builder  $scope
     * @return array{Action, Action}
     */
    protected static function recordOrderActions(Closure $scope): array
    {
        return [
            Action::make('moveUp')
                ->label('Naik')
                ->icon(Heroicon::OutlinedArrowUp)
                ->iconButton()
                ->tooltip('Naikkan urutan')
                ->color('gray')
                ->visible(fn (Model $record): bool => static::canMoveRecord($record, 'up', $scope))
                ->action(function (Model $record, Component $livewire) use ($scope): void {
                    static::moveRecord($record, 'up', $scope);
                    $livewire->dispatch('$refresh');
                }),
            Action::make('moveDown')
                ->label('Turun')
                ->icon(Heroicon::OutlinedArrowDown)
                ->iconButton()
                ->tooltip('Turunkan urutan')
                ->color('gray')
                ->visible(fn (Model $record): bool => static::canMoveRecord($record, 'down', $scope))
                ->action(function (Model $record, Component $livewire) use ($scope): void {
                    static::moveRecord($record, 'down', $scope);
                    $livewire->dispatch('$refresh');
                }),
        ];
    }

    /**
     * Determine whether the record can be moved in the given direction.
     *
     * @param  Closure(Model): Builder  $scope
     */
    private static function canMoveRecord(Model $record, string $direction, Closure $scope): bool
    {
        $keys = $scope($record)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck($record->getKeyName())
            ->all();

        $index = array_search($record->getKey(), $keys, true);

        if ($index === false) {
            return false;
        }

        return $direction === 'up' ? $index > 0 : $index < count($keys) - 1;
    }

    /**
     * Move the record one position up or down and renumber the scoped records.
     *
     * @param  Closure(Model): Builder  $scope
     */
    private static function moveRecord(Model $record, string $direction, Closure $scope): void
    {
        $records = $scope($record)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $index = $records->search(fn (Model $item): bool => $item->getKey() === $record->getKey());

        if ($index === false) {
            return;
        }

        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if (! $records->has($targetIndex)) {
            return;
        }

        $records = $records->values();
        [$records[$index], $records[$targetIndex]] = [$records[$targetIndex], $records[$index]];

        DB::transaction(function () use ($records): void {
            $records->each(function (Model $item, int $position): void {
                $item->update(['order' => $position + 1]);
            });
        });
    }
}
