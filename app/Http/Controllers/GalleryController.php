<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Video;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display published photo albums.
     */
    public function index(): View
    {
        $albums = Album::query()
            ->published()
            ->with('coverPhoto.media')
            ->withCount('photos')
            ->orderBy('order')
            ->orderByDesc('updated_at')
            ->paginate(12);

        return view('pages.gallery', [
            'albums' => $albums,
        ]);
    }

    /**
     * Display a published album and its ordered photos.
     */
    public function show(Album $album): View
    {
        abort_unless($album->is_published, 404);

        $album->load('coverPhoto.media')->loadCount('photos');

        $photos = $album->photos()
            ->with('media')
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(24)
            ->withQueryString();

        return view('pages.gallery-album', [
            'album' => $album,
            'photos' => $photos,
        ]);
    }

    /**
     * Display published videos.
     */
    public function videos(): View
    {
        $videos = Video::query()
            ->published()
            ->latest('published_at')
            ->paginate(12);

        return view('pages.gallery-videos', [
            'videos' => $videos,
        ]);
    }
}
