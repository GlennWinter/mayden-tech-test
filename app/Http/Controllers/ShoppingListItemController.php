<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingListItemRequest;
use App\Http\Requests\UpdateShoppingListItemRequest;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ShoppingListItemController extends Controller
{
    /**
     * @return Collection<int, ShoppingListItem>
     */
    public function index(ShoppingList $shoppingList): Collection
    {
        return $shoppingList->items()->get();
    }

    public function store(StoreShoppingListItemRequest $request, ShoppingList $shoppingList): JsonResponse
    {
        $item = $shoppingList->items()->create(
            $request->validated(),
        );

        return response()->json($item, 201);
    }

    public function update(
        UpdateShoppingListItemRequest $request,
        ShoppingList $shoppingList,
        ShoppingListItem $item
    ): JsonResponse {
        $item->update($request->validated());

        return response()->json($item);
    }

    // todo: add show function for individual item to show picture and details

    public function destroy(ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        $item->delete();

        return response()->json(null, 204);
    }
}
