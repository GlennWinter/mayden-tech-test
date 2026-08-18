<?php

namespace Database\Factories;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShoppingListItem>
 */
class ShoppingListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shopping_list_id' => ShoppingList::factory(),
            'name' => fake()->unique()->word(),
            'price_in_pence' => fake()->numberBetween(50, 1000),
            'quantity' => fake()->numberBetween(1, 5),
            'is_purchased' => false,
        ];
    }

    /**
     * Indicate that the item has been purchased.
     */
    public function purchased(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_purchased' => true,
        ]);
    }
}
