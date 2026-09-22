<?php

use App\Http\Controllers\Web\LeadDashboardController;
use Illuminate\Support\Facades\Route;

// Public Customer Lead Submission Form
Route::get('/', [LeadDashboardController::class, 'create'])->name('leads.create');
Route::get('/submit', [LeadDashboardController::class, 'create']);

// Supervisor Triage Dashboard
Route::get('/dashboard', [LeadDashboardController::class, 'index'])->name('leads.dashboard');
Route::patch('/leads/{lead}/status', [LeadDashboardController::class, 'updateStatus'])->name('leads.updateStatus');
