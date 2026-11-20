<?php
namespace App\Models;

use App\Enums\ResponseTime;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'ad_spend', 'leads', 'customers', 'customer_value',
        'gross_margin', 'close_rate', 'response_time', 'has_follow_up',
        'cpl', 'cpa', 'gross_profit_per_customer', 'break_even_cpl',
        'break_even_cpa', 'revenue', 'net_profit', 'roas',
        'score', 'score_label', 'bottleneck',
    ];

    protected $casts = [
        'ad_spend' => 'float',
        'customer_value' => 'float',
        'gross_margin' => 'float',
        'close_rate' => 'float',
        'response_time' => ResponseTime::class,
        'has_follow_up' => 'boolean',
        'cpl' => 'float',
        'cpa' => 'float',
        'gross_profit_per_customer' => 'float',
        'break_even_cpl' => 'float',
        'break_even_cpa' => 'float',
        'revenue' => 'float',
        'net_profit' => 'float',
        'roas' => 'float',
    ];

    protected static function booted(): void
    {
        static::creating(function (Submission $submission) {
            $submission->uuid = (string) Str::uuid();
        });
    }
}