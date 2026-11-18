<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ArticleComments\ArticleCommentResource;
use App\Models\ArticleComment;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingComments extends TableWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Komentar Menunggu Moderasi')
            ->description('Komentar baru yang belum disetujui.')
            ->query(ArticleComment::query()
                ->pending()
                ->with('article')
                ->latest()
                ->limit(5))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('article.title')
                    ->label('Artikel')
                    ->limit(45)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y H:i'),
            ])
            ->paginated(false)
            ->recordUrl(fn (ArticleComment $record): string => ArticleCommentResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('viewAllComments')
                    ->label('Lihat semua komentar')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->url(ArticleCommentResource::getUrl('index')),
            ]);
    }
}
