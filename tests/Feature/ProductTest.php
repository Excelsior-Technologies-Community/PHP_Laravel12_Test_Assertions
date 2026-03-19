<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_store_route_uses_form_request()
    {
        $this->assertRouteUsesFormRequest(
            'products.store',
            \App\Http\Requests\StoreProductRequest::class
        );
    }

    public function test_validation_rules_are_correct()
    {
        $expectedRules = [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
        ];

        $this->assertExactValidationRules(
            $expectedRules,
            (new \App\Http\Requests\StoreProductRequest())->rules()
        );
    }
}