<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 50,
            'category' => 'Electronics'
        ];

        $response = $this->postJson('/api/products', $productData);
        
        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => 'Test Product',
                'price' => 99.99,
                'stock' => 50,
                'category' => 'Electronics'
            ]
        ]);
        
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_validation_fails_when_name_missing()
    {
        $response = $this->postJson('/api/products', [
            'price' => 100,
            'stock' => 10,
            'category' => 'Test'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_products_list_api_works()
    {
        Product::factory()->create([
            'name' => 'Laptop',
            'price' => 50000,
            'stock' => 100,
            'category' => 'Electronics'
        ]);
        
        $response = $this->getJson('/api/products');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data' => [
                    '*' => ['id', 'name', 'price', 'stock', 'category']
                ],
                'current_page',
                'total',
                'per_page'
            ]
        ]);
        
        // Check that data exists
        $responseData = $response->json();
        $this->assertNotEmpty($responseData['data']['data']);
        $this->assertEquals('Laptop', $responseData['data']['data'][0]['name']);
    }

    public function test_validation_rules_are_correct()
    {
        // Test required fields
        $response = $this->postJson('/api/products', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'stock', 'category']);
        
        // Test numeric validation
        $response = $this->postJson('/api/products', [
            'name' => 'Test',
            'price' => 'not-numeric',
            'stock' => 'not-numeric',
            'category' => 'Test'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price', 'stock']);
        
        // Test min value validation
        $response = $this->postJson('/api/products', [
            'name' => 'Test',
            'price' => -1,
            'stock' => -1,
            'category' => 'Test'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price', 'stock']);
    }
}