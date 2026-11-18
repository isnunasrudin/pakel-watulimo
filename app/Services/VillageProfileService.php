<?php

namespace App\Services;

use App\Models\VillageSetting;

final class VillageProfileService
{
    private ?VillageSetting $settings = null;

    /**
     * Resolve the village profile once per request lifecycle.
     */
    public function settings(): VillageSetting
    {
        return $this->settings ??= VillageSetting::current();
    }
}
