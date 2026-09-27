<?php

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    use HasFactory;

    protected $table = 'legal_documents';

    protected $fillable = [
        'type',
        'version',
        'title',
        'body',
        'effective_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'effective_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public static function getCurrent(string $type): ?static
    {
        return static::where('type', $type)->where('is_current', true)->latest('id')->first();
    }
}
