<?php

namespace App\Filament\Resources\ArticleComments\Tables;

use App\Models\ArticleComment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ArticleCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('article.title')
                    ->label('Artikel')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        ArticleComment::STATUS_APPROVED => 'Disetujui',
                        ArticleComment::STATUS_REJECTED => 'Ditolak',
                        default => 'Menunggu',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        ArticleComment::STATUS_APPROVED => 'success',
                        ArticleComment::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        ArticleComment::STATUS_PENDING => 'Menunggu',
                        ArticleComment::STATUS_APPROVED => 'Disetujui',
                        ArticleComment::STATUS_REJECTED => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ArticleComment $record): bool => $record->status !== ArticleComment::STATUS_APPROVED)
                    ->action(fn (ArticleComment $record) => $record->update(['status' => ArticleComment::STATUS_APPROVED])),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ArticleComment $record): bool => $record->status !== ArticleComment::STATUS_REJECTED)
                    ->action(fn (ArticleComment $record) => $record->update(['status' => ArticleComment::STATUS_REJECTED])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Setujui terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => ArticleComment::STATUS_APPROVED]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
