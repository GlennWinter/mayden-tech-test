<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    public function index(): JsonResponse
    {
        // Only get shopping lists with items
        $shoppingLists = ShoppingList::with('items')->get();

        return response()->json($shoppingLists);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $shoppingList = ShoppingList::create($validated);

        return response()->json($shoppingList, 201);
    }

    public function show(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->load('items');

        return response()->json($shoppingList);
    }

    public function update(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        // Todo: move this to it's own Form Request class
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $shoppingList->update($validated);

        return response()->json($shoppingList);
    }

    public function destroy(ShoppingList $shoppingList): JsonResponse
    {
        $shoppingList->delete();

        return response()->json(null, 204);
    }
}
