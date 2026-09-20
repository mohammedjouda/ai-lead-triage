<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    protected $attributes = [
        'source' => 'website',
        'status' => 'new',
        'automation_status' => 'pending',
        'automation_attempts' => 0,
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'message',
        'source',
        'status',
        'automation_status',
        'automation_attempts',
        'automation_last_attempted_at',
        'automation_processed_at',
    ];

    protected function casts(): array
    {
        return [
            'automation_attempts' => 'integer',
            'automation_last_attempted_at' => 'datetime',
            'automation_processed_at' => 'datetime',
        ];
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(LeadAnalysis::class);
    }
}
