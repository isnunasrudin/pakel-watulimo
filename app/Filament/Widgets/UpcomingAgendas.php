<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Models\Agenda;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingAgendas extends TableWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Agenda Mendatang')
            ->description('Agenda terbit yang akan datang.')
            ->query(Agenda::query()
                ->published()
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5))
            ->columns([
                TextColumn::make('title')
                    ->label('Agenda')
                    ->wrap()
                    ->limit(70),
                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->placeholder('—')
                    ->limit(40),
            ])
            ->paginated(false)
            ->recordUrl(fn (Agenda $record): string => AgendaResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('createAgenda')
                    ->label('Tambah agenda')
                    ->icon(Heroicon::OutlinedPlus)
                    ->url(AgendaResource::getUrl('create')),
            ]);
    }
}
