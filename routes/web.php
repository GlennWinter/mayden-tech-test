<?php

use App\Models\ShoppingList;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('shopping-lists/Index');
})->name('home');

Route::get('/shopping-lists/{shoppingList}', function (
    ShoppingList $shoppingList
) {
    return Inertia::render('shopping-lists/Show', [
        'shoppingListId' => $shoppingList->id,
    ]);
})->name('shopping-lists.show');
