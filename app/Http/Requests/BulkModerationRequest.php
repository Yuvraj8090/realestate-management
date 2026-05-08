<?php

namespace App\Http\Requests;

use App\Enums\PropertyModerationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkModerationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'property_ids' => ['required', 'array', 'min:1'],
            'property_ids.*' => ['required', 'exists:properties,id'],
            'moderation_status' => ['required', Rule::in(PropertyModerationStatus::values())],
            'moderation_notes' => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
