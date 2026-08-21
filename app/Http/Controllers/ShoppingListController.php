<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingListRequest;
use App\Http\Requests\UpdateShoppingListRequest;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;

class ShoppingListController extends Controller
{
    /**
     * List all shopping lists with their items eager-loaded, so each list
     * includes its computed total_in_pence and is_over_budget attributes
     * without triggering N+1 queries.
     */
    public function index(): JsonResponse
    {
        // Only get shopping lists with items
        $shoppingLists = ShoppingList::with('items')->get();

        return response()->json($shoppingLists);
    }

    /**
     * Create a new shopping list. name is required; budget_limit_in_pence
     * is optional — a null budget means the list can never be "over budget".
     */
    public function store(StoreShoppingListRequest $request): JsonResponse
    {
        $shoppingList = ShoppingList::create($request->validated());

        return response()->json($shoppingList, 201);
    }

    /**
     * Get a single shopping list along with its items. 404s automatically
     * via route-model binding if the ID doesn't exist.
     */
    public function show(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->load('items');

        return response()->json($shoppingList);
    }

    /**
     * Partially or fully update a shopping list's name and/or budget.
     * All fields are optional (see UpdateShoppingListRequest) so this
     * supports both full and partial updates from the same endpoint.
     */
    public function update(UpdateShoppingListRequest $request, ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->update($request->validated());

        return response()->json($shoppingList);
    }

    /**
     * Delete a shopping list. Its items are removed automatically via
     * the cascadeOnDelete() foreign key defined in the items migration.
     */
    public function destroy(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->delete();

        return response()->json(null, 204);
    }
}
