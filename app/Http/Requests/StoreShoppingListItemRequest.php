<?php

namespace App\Http\Requests;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShoppingListItemRequest extends FormRequest
{
    // Authorization is intentionally unrestricted as authentication is out of scope.
    // With authentication, access would be restricted to the authenticated user's resources.
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $shoppingList = $this->route('shopping_list');
        $item = $this->route('item');

        if (! $shoppingList instanceof ShoppingList) {
            abort(404);
        }

        if ($item !== null && ! $item instanceof ShoppingListItem) {
            abort(404);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shopping_list_items', 'name')
                    ->where('shopping_list_id', $shoppingList->id)
                    ->ignore($item?->id),
            ],
            'price_in_pence' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
