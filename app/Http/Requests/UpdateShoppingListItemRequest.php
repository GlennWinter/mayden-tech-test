<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShoppingListItemRequest extends FormRequest
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
        return [
            'is_purchased' => ['sometimes', 'boolean'],
        ];
    }
}
