<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ShoppingList extends Model
{
    protected $fillable = [
        'name',
        'budget_limit_in_pence',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
