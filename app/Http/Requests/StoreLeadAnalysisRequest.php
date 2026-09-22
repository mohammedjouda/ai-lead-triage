<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intent' => [
                'required',
                'string',
                'in:website_development,ecommerce,automation,ai_integration,consulting,maintenance,other',
            ],
            'urgency' => ['required', 'string', 'in:low,medium,high'],
            'lead_score' => ['required', 'integer', 'between:0,100'],
            'summary' => ['required', 'string', 'max:2000'],
            'recommended_action' => ['required', 'string', 'max:1000'],
            'confidence' => ['nullable', 'numeric', 'between:0,1'],
            'raw_response' => ['nullable', 'array'],
        ];
    }
}
