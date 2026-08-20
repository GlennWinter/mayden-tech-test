<?php

use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use Illuminate\Support\Facades\Route;

Route::apiResource(
    'shopping-lists',
    ShoppingListController::class)
    ->middleware('throttle:shopping-list-api');

Route::apiResource(
    'shopping-lists.items',
    ShoppingListItemController::class)
    ->except(['show'])
    ->scoped([
        'shopping_list' => 'id',
        'item' => 'id',
    ])->middleware('throttle:shopping-list-api');
