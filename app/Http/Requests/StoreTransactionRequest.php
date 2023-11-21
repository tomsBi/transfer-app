<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AllowedCurrencies;

class StoreTransactionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'creditor_account_id' => 'required|uuid',
            'debtor_account_id' => 'required|uuid',
            'reference' => 'required|string|max:255',
            'currency' => ['required', 'string', 'max:3', new AllowedCurrencies],
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
