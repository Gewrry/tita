<?php

use App\Models\BusinessSetting;

if (!function_exists('business')) {
    /**
     * Get the current user's business settings.
     */
    function business(): ?BusinessSetting
    {
        if (!auth()->check()) {
            return null;
        }

        return BusinessSetting::current();
    }
}

if (!function_exists('is_sari_sari')) {
    function is_sari_sari(): bool
    {
        $business = business();
        return $business ? $business->isSariSari() : true;
    }
}

if (!function_exists('is_restaurant')) {
    function is_restaurant(): bool
    {
        $business = business();
        return $business ? $business->isRestaurant() : false;
    }
}
