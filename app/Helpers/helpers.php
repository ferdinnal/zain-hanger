<?php

if (! function_exists('setting')) {
    /**
     * Ambil nilai setting dari database.
     * Usage: setting('site_name', 'Default')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}
