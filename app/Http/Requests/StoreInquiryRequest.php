<?php

namespace App\Http\Requests;

use App\Enums\InquiryPreferredContactMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'preferred_contact_method' => ['required', Rule::in(InquiryPreferredContactMethod::values())],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => strip_tags((string) $this->input('message')),
        ]);
    }
}
