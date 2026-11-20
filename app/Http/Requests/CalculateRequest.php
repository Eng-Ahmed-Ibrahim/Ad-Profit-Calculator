<?php

namespace App\Http\Requests;

use App\Enums\ResponseTime;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CalculateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ad_spend' => 'required|numeric|gt:0',
            'leads' => 'required|integer|min:0',
            'customers' => 'required|integer|min:0|lte:leads',
            'customer_value' => 'required|numeric|gt:0',
            'gross_margin' => 'required|numeric|gt:0|max:100',
            'close_rate' => 'required|numeric|min:0|max:100',
            'response_time' => ['required', Rule::enum(ResponseTime::class)],
            'has_follow_up' => 'required|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'ad_spend.gt' => 'Monthly ad spend must be greater than 0.',
            'customers.lte' => 'Customers cannot be more than leads.',
            'customer_value.gt' => 'Average customer value must be greater than 0.',
            'gross_margin.gt' => 'Gross margin must be greater than 0.',
            'response_time.required' => 'Please select how fast you respond to leads.',
            'has_follow_up.required' => 'Please tell us whether you have a follow-up process.',
        ];
    }
}
