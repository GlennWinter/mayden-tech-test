<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShoppingListRequest;
use App\Http\Requests\UpdateShoppingListRequest;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;

class ShoppingListController extends Controller
{
    public function index(): JsonResponse
    {
        // Only get shopping lists with items
        $shoppingLists = ShoppingList::with('items')->get();

        return response()->json($shoppingLists);
    }

    public function store(StoreShoppingListRequest $request): JsonResponse
    {
        $shoppingList = ShoppingList::create($request->validated());

        return response()->json($shoppingList, 201);
    }

    public function show(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->load('items');

        return response()->json($shoppingList);
    }

    public function update(UpdateShoppingListRequest $request, ShoppingList $shoppingList): JsonResponse {
        $shoppingList->update($request->validated());

        return response()->json($shoppingList);
    }

    public function destroy(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->delete();

        return response()->json(null, 204);
    }
}
