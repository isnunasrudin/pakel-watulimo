<?php

namespace App\Filament\Resources\Articles\RelationManagers;

use App\Models\ArticleComment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Komentar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(80),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('Komentar')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        ArticleComment::STATUS_PENDING => 'Menunggu moderasi',
                        ArticleComment::STATUS_APPROVED => 'Disetujui',
                        ArticleComment::STATUS_REJECTED => 'Ditolak',
                    ])
                    ->required()
                    ->default(ArticleComment::STATUS_PENDING),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(70)
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
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
