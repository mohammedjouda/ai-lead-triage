<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'source' => [
                'nullable',
                'string',
                'in:website,instagram,whatsapp,facebook,manual,api',
            ],
        ];
    }
}
