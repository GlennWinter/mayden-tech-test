<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingListItemRequest;
use App\Http\Requests\UpdateShoppingListItemRequest;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\JsonResponse;

class ShoppingListItemController extends Controller
{
    /**
     * List all items belonging to the given shopping list.
     */
    public function index(ShoppingList $shoppingList): JsonResponse
    {
        return response()->json(
            $shoppingList->items()->get()
        );
    }

    /**
     * Add a new item to the given shopping list.
     */
    public function store(StoreShoppingListItemRequest $request, ShoppingList $shoppingList): JsonResponse
    {
        $item = $shoppingList->items()->create(
            $request->validated(),
        );

        return response()->json($item, 201);
    }

    /**
     * Update an existing item. Route-model binding is scoped to the parent
     * shopping list (see routes/api.php), so an item ID that belongs to a
     * different list resolves to a 404 here rather than being editable
     * cross-list.
     */
    public function update(
        UpdateShoppingListItemRequest $request,
        ShoppingList $shoppingList, // Required for Laravel's scoped nested route binding.
        ShoppingListItem $item
    ): JsonResponse {
        $item->update($request->validated());

        return response()->json($item);
    }

    // todo: add show function for individual item to show picture and details

    /**
     * Remove an item from the given shopping list. Same scoped-binding
     * protection as update() applies here.
     */
    public function destroy(
        ShoppingList $shoppingList, // Required for Laravel's scoped nested route binding.
        ShoppingListItem $item
    ): JsonResponse {
        $item->delete();

        return response()->json(null, 204);
    }
}
