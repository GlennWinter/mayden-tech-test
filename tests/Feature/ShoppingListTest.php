<?php

namespace Tests\Feature;

use App\Models\ShoppingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_shopping_lists(): void
    {
        ShoppingList::factory()->count(3)->create();

        $response = $this->getJson('/api/shopping-lists');

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_can_create_a_shopping_list(): void
    {
        $response = $this->postJson('/api/shopping-lists', [
            'name' => 'Weekly Shopping',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Weekly Shopping',
            ]);

        $this->assertDatabaseHas('shopping_lists', [
            'name' => 'Weekly Shopping',
        ]);
    }

    public function test_name_is_required_when_creating_a_shopping_list(): void
    {
        $response = $this->postJson('/api/shopping-lists', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_get_a_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->getJson(
            "/api/shopping-lists/{$shoppingList->id}"
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $shoppingList->id,
                'name' => $shoppingList->name,
            ]);
    }

    public function test_returns_not_found_for_unknown_shopping_list(): void
    {
        $response = $this->getJson('/api/shopping-lists/999');

        $response->assertNotFound();
    }

    public function test_can_update_a_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->patchJson(
            "/api/shopping-lists/{$shoppingList->id}",
            [
                'name' => 'New Name',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'New Name',
            ]);

        $this->assertDatabaseHas('shopping_lists', [
            'id' => $shoppingList->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_a_shopping_list(): void
    {
        $shoppingList = ShoppingList::factory()->create();

        $response = $this->deleteJson(
            "/api/shopping-lists/{$shoppingList->id}"
        );

        $response->assertSuccessful();

        $this->assertDatabaseMissing('shopping_lists', [
            'id' => $shoppingList->id,
        ]);
    }
}
