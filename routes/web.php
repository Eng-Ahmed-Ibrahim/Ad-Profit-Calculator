<?php

use App\Services\CalculationService;
use Illuminate\Support\Facades\Route;

Route::get('/1', function (CalculationService $service) {
    return response()->json(
        $service->calculate([
            'ad_spend' => 3000,
            'leads' => 60,
            'customers' => 6,
            'customer_value' => 2000,
            'gross_margin' => 60,
            'close_rate' => 10,
            'response_time' => 'under_5_min',
            'has_follow_up' => true,
        ])
    );
});
Route::get('/2', function (CalculationService $service) {
    return response()->json(
        $service->calculate([
            'ad_spend' => 5000,
            'leads' => 50,
            'customers' => 2,
            'customer_value' => 1500,
            'gross_margin' => 50,
            'close_rate' => 20,
            'response_time' => 'next_day_or_later',
            'has_follow_up' => false,
        ])
    );
});
Route::get('/3', function (CalculationService $service) {
    return response()->json(
        $service->calculate([
            'ad_spend' => 2000,
            'leads' => 0,
            'customers' => 0,
            'customer_value' => 1000,
            'gross_margin' => 40,
            'close_rate' => 10,
            'response_time' => 'same_day',
            'has_follow_up' => true,
        ])
    );
});
