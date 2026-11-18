<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestArticles extends TableWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Artikel Terbaru')
            ->description('Lima artikel terakhir yang masuk ke website.')
            ->query(Article::query()
                ->with(['category', 'author', 'media'])
                ->latest('published_at')
                ->limit(5))
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Sampul')
                    ->collection('cover')
                    ->conversion('thumb')
                    ->square()
                    ->imageSize(40),
                TextColumn::make('title')
                    ->label('Judul')
                    ->wrap()
                    ->limit(70),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (?Article $record): string => $record?->category?->color ?? 'gray')
                    ->placeholder('Tanpa kategori'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'published' ? 'Terbit' : 'Draft')
                    ->color(fn (string $state): string => $state === 'published' ? 'success' : 'gray'),
                TextColumn::make('published_at')
                    ->label('Terbit')
                    ->dateTime('d M Y')
                    ->placeholder('Belum terbit'),
            ])
            ->paginated(false)
            ->recordUrl(fn (Article $record): string => ArticleResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('createArticle')
                    ->label('Tulis artikel')
                    ->icon(Heroicon::OutlinedPlus)
                    ->url(ArticleResource::getUrl('create')),
            ]);
    }
}
