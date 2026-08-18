<?php

use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use Illuminate\Support\Facades\Route;

Route::apiResource('shopping-lists', ShoppingListController::class);
Route::apiResource(
    'shopping-lists.items',
    ShoppingListItemController::class)
    ->except(['show'])
    ->scoped([
        'shopping_list' => 'id',
        'item' => 'id',
    ]);
