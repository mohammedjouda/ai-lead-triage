<?php

use App\Http\Controllers\Web\LeadDashboardController;
use Illuminate\Support\Facades\Route;

// Public Customer Lead Submission Form
Route::get('/', [LeadDashboardController::class, 'create'])->name('leads.create');
Route::get('/submit', [LeadDashboardController::class, 'create']);
Route::post('/leads', [LeadDashboardController::class, 'store'])
    ->name('leads.store')
    ->middleware('throttle:lead-intake');

// Supervisor Triage Dashboard
Route::get('/dashboard', [LeadDashboardController::class, 'index'])->name('leads.dashboard');
Route::patch('/leads/{lead}/status', [LeadDashboardController::class, 'updateStatus'])->name('leads.updateStatus');
Route::post('/leads/{lead}/retry', [LeadDashboardController::class, 'retry'])
    ->name('leads.retry')
    ->middleware('throttle:lead-retry');

