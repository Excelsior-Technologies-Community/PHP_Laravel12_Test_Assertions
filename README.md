# PHP_Laravel12_Test_Assertions


## Project Description

PHP_Laravel12_Test_Assertions is a simple Laravel 12 project that demonstrates how to use the Laravel Test Assertions package to write clean and effective automated tests.

The project focuses on testing:

- Route behavior

- Form Request validation

- Controller integration

By using this package, developers can easily verify that their application follows best practices without writing complex test cases.


## Features

- Laravel 12 project setup

- Integration of Test Assertions package

- Route testing using assertRouteUsesFormRequest

- Validation rules testing using assertExactValidationRules

- Clean separation of validation using FormRequest

- Simple controller-based API response

- Beginner-friendly test examples



## Technologies Used

1. PHP 8+ – Backend programming language

2. Laravel 12 – PHP web framework

3. MySQL (optional) – Database

4. PHPUnit – Testing framework

5. Laravel Test Assertions Package – Advanced testing helpers



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Test_Assertions "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Test_Assertions

```

#### Explanation:

This command installs a fresh Laravel 12 project using Composer and creates a new project folder. 

The cd command moves you into the project directory to start development.




## STEP 2: Database Setup (Optional)

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_Test_Assertions
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_Test_Assertions

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

This step connects Laravel to a MySQL database. 

Running migrations creates default tables like users and jobs.





## STEP 3: Install Test Assertions Package

### Install package:

```
composer require --dev jasonmccreary/laravel-test-assertions

```


#### Explanation:

This installs the Laravel Test Assertions package, which provides helpful methods to test routes, validation rules, and controller behavior






## STEP 4: Update TestCase.php

### Open tests/TestCase.php and add the trait AdditionalAssertions:

```
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;

abstract class TestCase extends BaseTestCase
{
    use AdditionalAssertions;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}

```

#### Explanation:

This step adds the AdditionalAssertions trait so you can use advanced testing methods provided by the package.






## STEP 5: Create Controller & FormRequest

### Create Product Controller

```
php artisan make:controller ProductController

```

### File: app/Http/Controllers/ProductController.php

```
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request): JsonResponse
    {
        // Normally, save product to DB
        return response()->json(['success' => true]);
    }

    
}

```

#### Explanation:

This creates a controller to handle product-related requests.




### Create FormRequest

```
php artisan make:request StoreProductRequest

```

### File: app/Http/Requests/StoreProductRequest.php

```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all for demo
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'price' => 'required|numeric|min:1',
        ];
    }
}

```

#### Explanation:

FormRequest is used to handle validation logic separately from the controller.





## STEP 6: Add Route

### Edit routes/web.php:

```
use App\Http\Controllers\ProductController;

Route::post('/products', [ProductController::class, 'store'])->name('products.store');

```


#### Explanation:

This defines a POST route that connects the URL /products to the controller method.






## STEP 7: Create Test Class

### Run:

```
php artisan make:test ProductTest

```

### File: tests/Feature/ProductTest.php

```
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

```


#### Explanation:

This creates a feature test file where we will write test cases for our application.




## STEP 8: Run Tests

### Run: 

```
php artisan test

```


### Expected output (simplified):

```
OK (3 tests, 3 assertions)

```

#### Explanation:

This command runs all test cases and verifies that your routes and validation rules are working correctly.





## STEP 9: Run the App  (Optional)

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:

This starts the local Laravel development server so you can access the application in your browser.



## Test Output

### Below is the result after running:

```
php artisan test

```


<img src="screenshots/Screenshot 2026-03-19 100532.png" width="900">



---

## Project Folder Structure:

```
PHP_Laravel12_Test_Assertions/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ProductController.php
│   │   └── Requests/
│   │       └── StoreProductRequest.php
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   │   └── ProductTest.php
│   └── TestCase.php
├── composer.json
└── phpunit.xml

```
