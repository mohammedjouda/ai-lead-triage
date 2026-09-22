<?php

use App\Http\Controllers\Api\LeadAnalysisController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Middleware\VerifyAutomationSecret;
use Illuminate\Support\Facades\Route;

Route::get('/leads', [LeadController::class, 'index']);
Route::get('/leads/{lead}', [LeadController::class, 'show']);
Route::post('/leads', [LeadController::class, 'store']);

Route::post('/leads/{lead}/analysis', [LeadAnalysisController::class, 'store'])
    ->middleware(VerifyAutomationSecret::class);
