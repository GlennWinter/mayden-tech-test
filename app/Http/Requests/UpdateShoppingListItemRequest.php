<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShoppingListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('shopping_list_items', 'name')
                    ->where('shopping_list_id', $this->route('shopping_list')->id),
            ],
            'price_in_pence' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],
            'quantity' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
            ],
            'is_purchased' => ['sometimes', 'boolean'],
        ];
    }
}
