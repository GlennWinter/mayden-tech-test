<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShoppingListItemController extends Controller
{
    public function index(ShoppingList $shoppingList)
    {
        return $shoppingList->items;
    }

    public function store(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        // todo: move to own form request class
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price_in_pence' => ['required', 'integer', 'min:0'],
        ]);

        $item = $shoppingList->items()->create($validated);

        return response()->json($item, 201);
    }

    public function update(Request $request, ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'price_in_pence' => ['sometimes', 'integer', 'min:0'],
            'is_purchased' => ['sometimes', 'boolean'],
        ]);

        $item->update($validated);

        return response()->json($item);
    }

    // todo: add show function for individual item

    public function destroy(ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        $item->delete();

        return response()->json(null, 204);
    }
}
