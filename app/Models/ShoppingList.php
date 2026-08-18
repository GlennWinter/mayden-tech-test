<?php

namespace App\Models;

use Database\Factories\ShoppingListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    /** @use HasFactory<ShoppingListFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'budget_limit_in_pence',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
