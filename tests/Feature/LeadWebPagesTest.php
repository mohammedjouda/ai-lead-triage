<?php

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it renders the customer submission page successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('تواصل معنا لتطوير أعمالك')
        ->assertSee('نص الطلب أو الاستفسار غير المنظم');
});

test('it renders the supervisor dashboard with leads and metrics', function () {
    $lead = Lead::create([
        'name' => 'فارس النجار',
        'company' => 'شركة البناء الحديث',
        'email' => 'fares@example.com',
        'message' => 'نحتاج موقع احترافي لشركتنا مع بوابات دفع.',
        'source' => 'website',
        'status' => 'qualified',
        'automation_status' => 'processed',
    ]);

    $lead->analysis()->create([
        'intent' => 'website_development',
        'urgency' => 'high',
        'lead_score' => 90,
        'summary' => 'العميل يرغب في موقع حديث لشركته.',
        'recommended_action' => 'الاتصال بالعميل خلال 24 ساعة.',
        'confidence' => 0.95,
    ]);

    $response = $this->get('/dashboard');

    $response->assertStatus(200)
        ->assertSee('لوحة فرز وتحليل العملاء')
        ->assertSee('فارس النجار')
        ->assertSee('شركة البناء الحديث')
        ->assertSee('website_development')
        ->assertSee('90/100')
        ->assertSee('الاتصال بالعميل خلال 24 ساعة');
});

test('it allows supervisor to update lead status from dashboard', function () {
    $lead = Lead::create([
        'name' => 'جمال',
        'message' => 'استفسار عن الأسعار.',
        'status' => 'qualified',
    ]);

    $response = $this->patch("/leads/{$lead->id}/status", [
        'status' => 'contacted',
    ]);

    $response->assertRedirect();
    expect($lead->fresh()->status)->toBe('contacted');
});
