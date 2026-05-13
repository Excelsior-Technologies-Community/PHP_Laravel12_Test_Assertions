<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Route uses FormRequest
     */
    public function test_store_route_uses_form_request()
    {
        $this->assertRouteUsesFormRequest(
            'products.store',
            \App\Http\Requests\StoreProductRequest::class
        );
    }

    /**
     * Validation rules test
     */
    public function test_validation_rules_are_correct()
    {
        $expectedRules = [
            'name' => ['required', 'string', 'min:3'],
            'price' => ['required', 'numeric', 'min:1'],
            'category' => ['nullable', 'string'],
        ];

        $this->assertExactValidationRules(
            $expectedRules,
            (new \App\Http\Requests\StoreProductRequest())->rules()
        );
    }

    /**
     * Product create API test
     */
    public function test_product_can_be_created()
    {
        $response = $this->postJson('/products', [
            'name' => 'iPhone 15',
            'price' => 90000,
            'category' => 'Mobile',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15',
        ]);
    }

    /**
     * Validation error test
     */
    public function test_validation_fails_when_name_missing()
    {
        $response = $this->postJson('/products', [
            'price' => 500,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Product list API test
     */
    public function test_products_list_api_works()
    {
        Product::factory()->create([
            'name' => 'Laptop',
            'price' => 50000,
        ]);

        $response = $this->getJson('/products');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}