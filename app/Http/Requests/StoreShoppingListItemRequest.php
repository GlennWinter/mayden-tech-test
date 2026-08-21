<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShoppingListItemRequest extends FormRequest
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
                    ->where('shopping_list_id', $this->route('shopping_list')->id)
                    ->ignore($this->route('item')?->id), // for update
            ],
            'price_in_pence' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
