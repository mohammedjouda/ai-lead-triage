<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'intent',
        'urgency',
        'lead_score',
        'summary',
        'recommended_action',
        'confidence',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'lead_score' => 'integer',
            'confidence' => 'float',
            'raw_response' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
