<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'edrpou'  => ['required', 'string', 'min:8', 'max:10'],
            'address' => ['required', 'string'],
        ];
    }
}
