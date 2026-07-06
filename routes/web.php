<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestSuiteController;

Route::get('/', [TestSuiteController::class, 'index'])->name('testing.dashboard');
Route::get('/testing/run/architecture', [TestSuiteController::class, 'runArchitectureTest']);
Route::get('/testing/run/jsonschema', [TestSuiteController::class, 'runJsonSchemaTest']);
Route::get('/testing/run/audit', [TestSuiteController::class, 'runDatabaseAuditTest']);
Route::get('/testing/run/validation', [TestSuiteController::class, 'runValidationTest']);
Route::get('/testing/run/httpmock', [TestSuiteController::class, 'runHttpMockTest']);