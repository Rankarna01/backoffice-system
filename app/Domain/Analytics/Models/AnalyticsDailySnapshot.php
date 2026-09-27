<?php

namespace App\Domain\Analytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsDailySnapshot extends Model
{
    use HasFactory;

    protected $table = 'analytics_daily_snapshots';

    protected $fillable = [
        'snapshot_date',
        'gross_revenue',
        'net_revenue',
        'new_orders_count',
        'paid_orders_count',
        'new_users_count',
        'active_subscribers_count',
        'course_enrollments_count',
        'lesson_completions_count',
        'quiz_attempts_count',
        'live_attendees_count',
        'category_revenue_distribution',
        'payment_methods_distribution',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'gross_revenue' => 'decimal:2',
            'net_revenue' => 'decimal:2',
            'new_orders_count' => 'integer',
            'paid_orders_count' => 'integer',
            'new_users_count' => 'integer',
            'active_subscribers_count' => 'integer',
            'course_enrollments_count' => 'integer',
            'lesson_completions_count' => 'integer',
            'quiz_attempts_count' => 'integer',
            'live_attendees_count' => 'integer',
            'category_revenue_distribution' => 'array',
            'payment_methods_distribution' => 'array',
            'meta' => 'array',
        ];
    }
}
