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

    protected $appends = [
        'total_in_pence',
        'is_over_budget',
    ];

    /**
     * @return HasMany<ShoppingListItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function getTotalInPenceAttribute(): int
    {
        return $this->items->sum(
            fn (ShoppingListItem $item) => $item->price_in_pence * $item->quantity
        );
    }

    public function getIsOverBudgetAttribute(): bool
    {
        if ($this->budget_limit_in_pence === null) {
            return false;
        }

        return $this->total_in_pence > $this->budget_limit_in_pence;
    }
}
