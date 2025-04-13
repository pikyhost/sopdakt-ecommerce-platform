<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return 'contact_settings';
    }

    /**
     * Find a setting by key or return default value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Update or create a setting.
     *
     * @param string $key
     * @param mixed $value
     * @return ContactSetting
     */
    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all settings as an associative array.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return static::where('key', $key)->exists();
    }
}
