<?php

namespace App\Models;

use Database\Factories\ShoppingListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    /** @use HasFactory<ShoppingListFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'budget_limit_in_pence',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
