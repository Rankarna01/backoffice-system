<?php

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'group',
        'name',
        'payload',
        'locked',
    ];

    protected function casts(): array
    {
        return [
            'locked' => 'boolean',
        ];
    }

    public static function get(string $name, mixed $default = null, string $group = 'general'): mixed
    {
        $setting = static::where('group', $group)->where('name', $name)->first();
        if (! $setting || $setting->payload === null) {
            return $default;
        }

        $decoded = json_decode($setting->payload, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $setting->payload;
    }

    public static function set(string $name, mixed $value, string $group = 'general'): static
    {
        $payload = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        return static::updateOrCreate(
            ['group' => $group, 'name' => $name],
            ['payload' => $payload]
        );
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(function ($item) {
                $decoded = json_decode($item->payload, true);
                $val = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $item->payload;
                return [$item->name => $val];
            })
            ->all();
    }

    public static function setGroup(string $group, array $values): void
    {
        foreach ($values as $name => $value) {
            static::set($name, $value, $group);
        }
    }
}
