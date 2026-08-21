<?php

namespace Tests\Feature;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_items_for_a_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        ShoppingListItem::factory()
            ->count(3)
            ->for($shoppingList)
            ->create();

        $response = $this->getJson(
            "/api/shopping-lists/{$shoppingList->id}/items"
        );

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_only_returns_items_belonging_to_the_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();
        $otherShoppingList = ShoppingList::factory()->create();

        ShoppingListItem::factory()
            ->count(2)
            ->for($shoppingList)
            ->create();

        ShoppingListItem::factory()
            ->count(3)
            ->for($otherShoppingList)
            ->create();

        $response = $this->getJson(
            "/api/shopping-lists/{$shoppingList->id}/items"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_can_add_an_item_to_a_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'quantity' => 2,
                'price_in_pence' => 150,
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Milk',
                'quantity' => 2,
                'price_in_pence' => 150,
            ]);

        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $shoppingList->id,
            'name' => 'Milk',
            'quantity' => 2,
            'price_in_pence' => 150,
        ]);
    }

    public function test_name_is_required_when_creating_an_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'quantity' => 1,
                'price_in_pence' => 100,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_quantity_is_required_when_creating_an_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'price_in_pence' => 100,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_price_in_pence_is_required_when_creating_an_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'quantity' => 1,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price_in_pence']);
    }

    public function test_quantity_must_be_at_least_one(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'quantity' => 0,
                'price_in_pence' => 100,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_price_in_pence_cannot_be_negative(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'quantity' => 1,
                'price_in_pence' => -1,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price_in_pence']);
    }

    public function test_can_update_a_shopping_list_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $item = ShoppingListItem::factory()
            ->for($shoppingList)
            ->create([
                'is_purchased' => false,
            ]);

        $response = $this->patchJson(
            "/api/shopping-lists/{$shoppingList->id}/items/{$item->id}",
            [
                'is_purchased' => true,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'is_purchased' => true,
            ]);

        $this->assertDatabaseHas('shopping_list_items', [
            'is_purchased' => true,
        ]);
    }

    public function test_can_partially_update_a_shopping_list_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $item = ShoppingListItem::factory()
            ->for($shoppingList)
            ->create([
                'is_purchased' => false,
            ]);

        $response = $this->patchJson(
            "/api/shopping-lists/{$shoppingList->id}/items/{$item->id}",
            [
                'is_purchased' => true,
            ]
        );

        $response->assertOk();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'is_purchased' => true,
        ]);
    }

    public function test_can_delete_a_shopping_list_item(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $item = ShoppingListItem::factory()
            ->for($shoppingList)
            ->create();

        $response = $this->deleteJson(
            "/api/shopping-lists/{$shoppingList->id}/items/{$item->id}"
        );

        $response->assertSuccessful();

        $this->assertDatabaseMissing('shopping_list_items', [
            'id' => $item->id,
        ]);
    }

    public function test_item_cannot_be_updated_through_another_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();
        $otherShoppingList = ShoppingList::factory()->create();

        $item = ShoppingListItem::factory()
            ->for($otherShoppingList)
            ->create([
                'price_in_pence' => 100,
            ]);

        $response = $this->patchJson(
            "/api/shopping-lists/{$shoppingList->id}/items/{$item->id}",
            [
                'name' => 'Updated Name',
            ]
        );

        $response->assertNotFound();
    }

    public function test_item_cannot_be_deleted_through_another_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();
        $otherShoppingList = ShoppingList::factory()->create();

        $item = ShoppingListItem::factory()
            ->for($otherShoppingList)
            ->create();

        $response = $this->deleteJson(
            "/api/shopping-lists/{$shoppingList->id}/items/{$item->id}"
        );

        $response->assertNotFound();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
        ]);
    }

    public function test_duplicate_item_name_returns_validation_error(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        ShoppingListItem::factory()
            ->for($shoppingList)
            ->create([
                'name' => 'Milk',
            ]);

        $response = $this->postJson(
            "/api/shopping-lists/{$shoppingList->id}/items",
            [
                'name' => 'Milk',
                'quantity' => 1,
                'price_in_pence' => 100,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseCount('shopping_list_items', 1);
    }
}
