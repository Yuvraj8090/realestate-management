<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'filters' => ['required', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('filters'))) {
            $decoded = json_decode($this->input('filters'), true);

            if (is_array($decoded)) {
                $this->merge([
                    'filters' => $decoded,
                ]);
            }
        }
    }
}
