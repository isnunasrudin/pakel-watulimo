<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Filament\Resources\Albums\AlbumResource;
use App\Filament\Resources\ArticleComments\ArticleCommentResource;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Agenda;
use App\Models\Album;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Photo;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?bool $isLazyLoaded = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $articles = Article::query()->count();
        $publishedArticles = Article::query()->published()->count();
        $agendas = Agenda::query()->published()->where('starts_at', '>=', now())->count();
        $albums = Album::query()->published()->count();
        $photos = Photo::query()->count();
        $users = User::query()->count();
        $admins = User::query()->where('is_admin', true)->count();
        $pendingComments = ArticleComment::query()->pending()->count();
        $comments = ArticleComment::query()->count();

        return [
            Stat::make('Artikel', number_format($publishedArticles, 0, ',', '.'))
                ->description($articles.' total')
                ->descriptionIcon(Heroicon::OutlinedDocumentText)
                ->color('success')
                ->url(ArticleResource::getUrl('index')),
            Stat::make('Agenda Mendatang', number_format($agendas, 0, ',', '.'))
                ->description('Agenda terbit')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('info')
                ->url(AgendaResource::getUrl('index')),
            Stat::make('Galeri Foto', number_format($photos, 0, ',', '.'))
                ->description($albums.' album terbit')
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('info')
                ->url(AlbumResource::getUrl('index')),
            Stat::make('Komentar Pending', number_format($pendingComments, 0, ',', '.'))
                ->description($comments.' total komentar')
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color($pendingComments > 0 ? 'warning' : 'gray')
                ->url(ArticleCommentResource::getUrl('index')),
            Stat::make('Pengguna', number_format($users, 0, ',', '.'))
                ->description($admins.' administrator')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('gray')
                ->url(UserResource::getUrl('index')),
        ];
    }
}
