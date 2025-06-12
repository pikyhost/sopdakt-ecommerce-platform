<?php

namespace App\Helpers;

use App\Models\ContactSetting;

class ContactSettings
{
    public static function get($key, $default = null)
    {
        $setting = ContactSetting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function all()
    {
        return ContactSetting::pluck('value', 'key')->toArray();
    }
}
