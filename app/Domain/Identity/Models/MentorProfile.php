<?php

namespace App\Domain\Identity\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_url',
        'headline',
        'experience_years',
        'trading_style',
        'win_rate',
        'portfolio_url',
        'certifications',
        'portfolio_items',
        'bio',
        'expertise',
        'social_links',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'win_rate' => 'decimal:2',
            'certifications' => 'array',
            'portfolio_items' => 'array',
            'expertise' => 'array',
            'social_links' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
