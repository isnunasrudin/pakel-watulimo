<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/berita', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/berita/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/berita/{article:slug}/komentar', [CommentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('articles.comments.store');

Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/profil', [PageController::class, 'profile'])->name('pages.profile');
Route::get('/pemerintahan', [PageController::class, 'officials'])->name('pages.officials');
Route::get('/layanan', [PageController::class, 'services'])->name('pages.services');
Route::get('/kontak', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/halaman/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/galeri/foto', [GalleryController::class, 'index'])
    ->name('gallery.photos');
Route::get('/galeri/foto/{album:slug}', [GalleryController::class, 'show'])
    ->name('gallery.album');
Route::get('/galeri/video', [GalleryController::class, 'videos'])
    ->name('gallery.videos');
