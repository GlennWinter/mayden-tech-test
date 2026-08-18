<?php

namespace App\Models;

use Database\Factories\ShoppingListItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    /** @use HasFactory<ShoppingListItemFactory> */
    use HasFactory;

    protected $fillable = [
        'shopping_list_id',
        'name',
        'price_in_pence',
        'quantity',
        'is_purchased',
    ];

    protected $casts = [
        'is_purchased' => 'boolean',
    ];

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }
}
