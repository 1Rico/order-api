<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'discount_amount' => 'required|integer|min:1',
            'voucher_code' => 'required|unique:vouchers|max:30'
        ];
    }
}
