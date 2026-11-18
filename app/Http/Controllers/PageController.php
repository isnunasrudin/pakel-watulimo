<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\ServiceLink;
use App\Models\SocialLink;
use App\Models\VillageOfficial;
use App\Models\VillagePillar;
use App\Models\VillageStatistic;
use App\Services\VillageProfileService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private VillageProfileService $villageProfile) {}

    /**
     * Display the village profile and public statistics.
     */
    public function profile(): View
    {
        return view('pages.village.profile', [
            'villageSettings' => $this->villageProfile->settings(),
            'statistics' => VillageStatistic::query()->ordered()->get(),
        ]);
    }

    /**
     * Display the ordered list of village officials.
     */
    public function officials(): View
    {
        return view('pages.village.officials', [
            'villageSettings' => $this->villageProfile->settings(),
            'officials' => VillageOfficial::query()->ordered()->get(),
            'pillars' => VillagePillar::query()->ordered()->get(),
        ]);
    }

    /**
     * Display available village services.
     */
    public function services(): View
    {
        return view('pages.village.services', [
            'villageSettings' => $this->villageProfile->settings(),
            'serviceLinks' => ServiceLink::query()->ordered()->get(),
        ]);
    }

    /**
     * Display village contact details.
     */
    public function contact(): View
    {
        return view('pages.village.contact', [
            'villageSettings' => $this->villageProfile->settings(),
            'socialLinks' => SocialLink::query()->ordered()->get(),
        ]);
    }

    /**
     * Display a published custom page.
     */
    public function show(Page $page): View
    {
        abort_unless($page->is_published, 404);

        $page->load('media');

        return view('pages.page', [
            'page' => $page,
            'villageSettings' => $this->villageProfile->settings(),
        ]);
    }
}
