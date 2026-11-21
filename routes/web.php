<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubmissionController;


Route::prefix("/")->controller(SubmissionController::class)->group(function () {
    Route::get('/', 'index')->name('submission.index');
    Route::get('/results/{uuid}', 'results')->name('submission.results');
    Route::post('/submit', 'store')->name('submission.store');
});
