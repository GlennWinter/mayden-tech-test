<?php

namespace App\Http\Requests;

use App\Models\ShoppingList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShoppingListRequest extends FormRequest
{
    // Authorization is intentionally unrestricted as authentication is out of scope.
    // With authentication, access would be restricted to the authenticated user's resources.
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        $shoppingList = $this->route('shopping_list');

        if ($shoppingList !== null && ! $shoppingList instanceof ShoppingList) {
            abort(404);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shopping_lists', 'name')
                    ->ignore($shoppingList?->id),
            ],
            'budget_limit_in_pence' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }
}
