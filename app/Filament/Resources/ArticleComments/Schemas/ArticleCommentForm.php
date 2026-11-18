<?php

namespace App\Filament\Resources\ArticleComments\Schemas;

use App\Models\ArticleComment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArticleCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('article_id')
                    ->label('Artikel')
                    ->relationship('article', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                TextInput::make('ip_address')
                    ->label('IP address')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
