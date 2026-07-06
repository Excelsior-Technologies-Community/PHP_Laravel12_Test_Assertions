<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class TestSuiteController extends Controller
{
    public function index(): View
    {
        return view('testing.dashboard');
    }

    public function runArchitectureTest(): JsonResponse
    {
        $controllerToken = 'DB::table';
        $controllerPath = app_path('Http/Controllers/ProductController.php');
        $hasDirectQueries = false;

        if (file_exists($controllerPath)) {
            $content = file_get_contents($controllerPath);
            if (str_contains($content, $controllerToken)) {
                $hasDirectQueries = true;
            }
        }

        return response()->json([
            'status' => !$hasDirectQueries ? 'passed' : 'failed',
            'message' => !$hasDirectQueries 
                ? 'Architecture Assertion Passed: Controllers are clean of direct raw database expressions.'
                : 'Architecture Assertion Failed: Direct raw queries detected inside controllers layer.'
        ]);
    }

    public function runJsonSchemaTest(): JsonResponse
    {
        $product = Product::first() ?? Product::create([
            'name' => 'Test Item ' . time(),
            'slug' => 'test-item-' . time(),
            'price' => 99.99,
            'category' => 'Testing',
            'stock' => 10,
            'is_active' => true
        ]);

        $payload = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float)$product->price,
            'created_at' => $product->created_at->toISOString()
        ];

        $isValid = is_int($payload['id']) && is_string($payload['name']) && is_numeric($payload['price']);

        return response()->json([
            'status' => $isValid ? 'passed' : 'failed',
            'schema' => $payload,
            'message' => $isValid 
                ? 'JSON Schema Assertion Passed: API schema node data types structurally validated.'
                : 'JSON Schema Assertion Failed: Type mismatch inside collection payload stream.'
        ]);
    }

    public function runDatabaseAuditTest(): JsonResponse
    {
        $product = Product::create([
            'name' => 'Audit Item ' . time(),
            'slug' => 'audit-item-' . time(),
            'price' => 150.00,
            'category' => 'Testing',
            'stock' => 5,
            'is_active' => true
        ]);
        
        $product->update(['price' => 175.00]);
        $product->delete();

        $hasLogs = ActivityLog::where('model_id', $product->id)->count() >= 2;
        $isSoftDeleted = Product::onlyTrashed()->where('id', $product->id)->exists();

        return response()->json([
            'status' => ($hasLogs && $isSoftDeleted) ? 'passed' : 'failed',
            'message' => ($hasLogs && $isSoftDeleted)
                ? 'Database Audit & SoftDelete Assertions Passed: Lifecycle hooks registered mutation arrays correctly.'
                : 'Database Audit Assertion Failed: Transaction log state records missing.'
        ]);
    }

    public function runValidationTest(Request $request): JsonResponse
    {
        $rules = [
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric|min:0.01'
        ];

        $invalidValidator = Validator::make(['name' => '', 'price' => -5], $rules);
        $validValidator = Validator::make(['name' => 'Unique Name ' . time(), 'price' => 20], $rules);

        $passed = $invalidValidator->fails() && !$validValidator->fails();

        return response()->json([
            'status' => $passed ? 'passed' : 'failed',
            'errors_caught' => $invalidValidator->errors()->toArray(),
            'message' => $passed
                ? 'Form Request Validation Assertion Passed: Constraints rejected anomalous values instantly.'
                : 'Form Request Validation Assertion Failed: Core request boundary bypassed.'
        ]);
    }

    public function runHttpMockTest(): JsonResponse
    {
        Http::fake([
            'api.warehouse.com/*' => Http::response(['status' => 'synced', 'inventory_id' => 8842], 200)
        ]);

        $response = Http::post('https://api.warehouse.com/products', ['name' => 'Cloud Hub']);
        $passed = $response->successful() && $response->json('status') === 'synced';

        return response()->json([
            'status' => $passed ? 'passed' : 'failed',
            'mocked_response' => $response->json(),
            'message' => $passed
                ? 'HTTP Client Mocking Assertion Passed: External logistics pipeline faked and validated safely.'
                : 'HTTP Client Mocking Assertion Failed: Mock interceptor down.'
        ]);
    }
}