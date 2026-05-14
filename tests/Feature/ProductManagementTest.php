<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'stock' => 50,
            'category' => 'Electronics'
        ];

        $response = $this->postJson('/api/products', $productData);
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 50,
            'category' => 'Electronics'
        ]);
        
        // Check structure inside data key
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id', 'name', 'slug', 'description', 
                'price', 'stock', 'category', 'is_active',
                'created_at', 'updated_at'
            ]
        ]);
        
        $product = Product::where('name', 'Test Product')->first();
        $this->assertEquals('test-product', $product->slug);
    }

    public function test_product_validation_errors()
    {
        // Test empty request
        $response = $this->postJson('/api/products', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'price', 'stock', 'category']);
        
        // Test invalid data
        $invalidData = [
            'name' => 'Te',
            'price' => -10,
            'stock' => -5,
            'category' => 'Test'
        ];
        
        $response = $this->postJson('/api/products', $invalidData);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price', 'stock']);
    }

    public function test_can_get_products_list()
    {
        Product::factory()->count(5)->create();
        
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
        
        $this->assertEquals(5, Product::count());
    }

    public function test_can_get_single_product()
    {
        $product = Product::factory()->create([
            'name' => 'Unique Product',
            'price' => 199.99
        ]);
        
        $response = $this->getJson("/api/products/{$product->id}");
        
        $response->assertStatus(200);
        // Check inside data key
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => 'Unique Product',
                'price' => 199.99
            ]
        ]);
        
        // Test not found
        $response = $this->getJson('/api/products/99999');
        $response->assertStatus(404);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create([
            'name' => 'Original Name',
            'price' => 100.00
        ]);
        
        $updateData = [
            'name' => 'Updated Name',
            'price' => 150.00,
            'stock' => 75
        ];
        
        $response = $this->putJson("/api/products/{$product->id}", $updateData);
        
        $response->assertStatus(200);
        // Check inside data key
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => 'Updated Name',
                'price' => 150.00,
                'stock' => 75
            ]
        ]);
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 150.00,
            'stock' => 75
        ]);
        
        $updatedProduct = Product::find($product->id);
        $this->assertEquals('updated-name', $updatedProduct->slug);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();
        
        $response = $this->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        
        $response = $this->getJson("/api/products/{$product->id}");
        $response->assertStatus(404);
    }

    public function test_can_toggle_product_active_status()
    {
        $product = Product::factory()->create(['is_active' => true]);
        
        $response = $this->patchJson("/api/products/{$product->id}/toggle-active");
        $response->assertStatus(200);
        // Check inside data key
        $response->assertJson([
            'success' => true,
            'data' => ['is_active' => false]
        ]);
        
        $response = $this->patchJson("/api/products/{$product->id}/toggle-active");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => ['is_active' => true]
        ]);
        
        $product->refresh();
        $this->assertTrue($product->is_active);
    }

    public function test_product_scopes()
    {
        Product::factory()->count(3)->create(['is_active' => true, 'stock' => 10]);
        Product::factory()->count(2)->create(['is_active' => false, 'stock' => 10]);
        Product::factory()->count(2)->create(['is_active' => true, 'stock' => 0]);
        
        $activeProducts = Product::active()->get();
        $this->assertCount(5, $activeProducts);
        
        $inStockProducts = Product::inStock()->get();
        $this->assertCount(5, $inStockProducts);
        
        $activeAndInStock = Product::active()->inStock()->get();
        $this->assertCount(3, $activeAndInStock);
    }

    public function test_batch_product_operations()
    {
        $products = Product::factory()->count(5)->create(['is_active' => true]);
        
        foreach ($products as $product) {
            $this->patchJson("/api/products/{$product->id}/toggle-active");
        }
        
        foreach ($products as $product) {
            $product->refresh();
            $this->assertFalse($product->is_active);
        }
    }

    public function test_product_price_calculations()
    {
        $products = collect([
            Product::factory()->create(['price' => 10.00, 'stock' => 5]),
            Product::factory()->create(['price' => 20.00, 'stock' => 3]),
            Product::factory()->create(['price' => 30.00, 'stock' => 2]),
        ]);
        
        $totalValue = $products->sum(fn($p) => $p->price * $p->stock);
        $this->assertEquals(170.00, $totalValue);
        
        $avgPrice = $products->avg('price');
        $this->assertEquals(20.00, $avgPrice);
        
        $maxPrice = $products->max('price');
        $this->assertEquals(30.00, $maxPrice);
    }

    public function test_product_filtering()
    {
        Product::factory()->create(['category' => 'Electronics', 'price' => 100]);
        Product::factory()->create(['category' => 'Electronics', 'price' => 200]);
        Product::factory()->create(['category' => 'Clothing', 'price' => 50]);
        
        $electronics = Product::where('category', 'Electronics')->get();
        $this->assertCount(2, $electronics);
        
        $expensiveProducts = Product::where('price', '>', 150)->get();
        $this->assertCount(1, $expensiveProducts);
        $this->assertEquals(200, $expensiveProducts->first()->price);
        
        $filtered = Product::where('category', 'Electronics')
                          ->where('price', '<', 150)
                          ->get();
        $this->assertCount(1, $filtered);
    }

    public function test_product_data_integrity()
    {
        $product = Product::factory()->create(['name' => 'Unique Name']);
        
        // Test duplicate name
        $duplicateData = [
            'name' => $product->name,
            'description' => 'Duplicate',
            'price' => 100,
            'stock' => 10,
            'category' => 'Test'
        ];
        
        $this->postJson('/api/products', $duplicateData)
             ->assertStatus(422);
        
        // Test negative price
        $invalidProduct = [
            'name' => 'Test Product',
            'description' => 'Test',
            'price' => -5,
            'stock' => 10,
            'category' => 'Test'
        ];
        $this->postJson('/api/products', $invalidProduct)
             ->assertStatus(422);
        
        // Test negative stock
        $invalidStock = [
            'name' => 'Test Product 2',
            'description' => 'Test',
            'price' => 100,
            'stock' => -1,
            'category' => 'Test'
        ];
        $this->postJson('/api/products', $invalidStock)
             ->assertStatus(422);
    }

    public function test_api_response_time()
    {
        Product::factory()->count(10)->create();
        
        $startTime = microtime(true);
        $response = $this->getJson('/api/products');
        $endTime = microtime(true);
        
        $responseTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        $this->assertLessThan(1000, $responseTime);
    }

    public function test_product_pagination()
    {
        Product::factory()->count(25)->create();
        
        $response = $this->getJson('/api/products?page=1&per_page=10');
        $response->assertStatus(200);
        
        $responseData = $response->json();
        // Access nested data structure
        $this->assertEquals(25, $responseData['data']['total']);
        $this->assertEquals(10, $responseData['data']['per_page']);
        $this->assertEquals(1, $responseData['data']['current_page']);
        $this->assertEquals(3, $responseData['data']['last_page']);
        
        $response = $this->getJson('/api/products?page=2&per_page=10');
        $responseData = $response->json();
        $this->assertEquals(2, $responseData['data']['current_page']);
    }
}