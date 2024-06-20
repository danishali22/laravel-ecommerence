<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.   1    54    56
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);
        
        $subCategories = [8,9];
        $subRandCategories = array_rand($subCategories);

        $brand = [1,2,3,4,5,6];
        $brandRand = array_rand($brand);

        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => 5,
            'sub_category_id' => $subCategories[$subRandCategories],
            'brand_id' => $brand[$brandRand],
            'price' => rand(100,10000),
            'sku' => rand(100,100000),
            'track_qty' => 'Yes',
            'qty' => rand(2,20),
            'is_featured' => 'Yes',
            'status' => 1,

        ];
    }
}
