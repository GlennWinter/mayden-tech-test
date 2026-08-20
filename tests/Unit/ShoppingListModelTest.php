<?php

namespace Tests\Unit;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_the_total_in_pence(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 250,
            'quantity' => 2,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 100,
            'quantity' => 1,
        ]);

        $this->assertSame(600, $shoppingList->total_in_pence);
    }

    public function test_it_is_over_budget_when_total_exceeds_budget(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'budget_limit_in_pence' => 500,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 600,
            'quantity' => 1,
        ]);

        $this->assertTrue($shoppingList->is_over_budget);
    }

    public function test_it_is_not_over_budget_when_total_is_within_budget(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'budget_limit_in_pence' => 1000,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 500,
            'quantity' => 1,
        ]);

        $this->assertFalse($shoppingList->is_over_budget);
    }

    public function test_it_is_not_over_budget_when_no_budget_is_set(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'budget_limit_in_pence' => null,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 10000,
            'quantity' => 1,
        ]);

        $this->assertFalse($shoppingList->is_over_budget);
    }

    public function test_it_is_not_over_budget_when_total_equals_budget(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'budget_limit_in_pence' => 500,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 500,
            'quantity' => 1,
        ]);

        $this->assertFalse($shoppingList->is_over_budget);
    }

    public function test_empty_shopping_list_has_zero_total(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $this->assertSame(0, $shoppingList->total_in_pence);
    }

    public function test_total_accounts_for_item_quantities(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 250,
            'quantity' => 4,
        ]);

        $this->assertSame(1000, $shoppingList->total_in_pence);
    }

    public function test_total_equal_to_budget_is_not_over_budget(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'budget_limit_in_pence' => 1000,
        ]);

        ShoppingListItem::factory()->create([
            'shopping_list_id' => $shoppingList->id,
            'price_in_pence' => 500,
            'quantity' => 2,
        ]);

        $this->assertFalse($shoppingList->is_over_budget);
    }
}
