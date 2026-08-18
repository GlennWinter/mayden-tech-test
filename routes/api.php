<?php

use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use Illuminate\Support\Facades\Route;

Route::apiResource('shopping-lists', ShoppingListController::class);
Route::apiResource('shopping-list-items', ShoppingListItemController::class);
