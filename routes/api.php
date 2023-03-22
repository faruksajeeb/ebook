<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::apiResource('/manage-employee',EmployeeController::class);
Route::apiResource('/manage-supplier',SupplierController::class);
Route::apiResource('/manage-customer',CustomerController::class);
Route::apiResource('/manage-category',CategoryController::class);
Route::apiResource('/manage-product',ProductController::class);
Route::apiResource('/manage-expense',ExpenseController::class);

Route::post('/salary/paid/{id}', [SalaryController::class,'Paid']);
Route::get('/salary', [SalaryController::class,'AllSalary']);

Route::get('/salary/view/{id}', [SalaryController::class,'ViewSalary']);
Route::get('/edit/salary/{id}',[ SalaryController::class,'EditSalary']);
Route::post('/salary/update/{id}', [SalaryController::class,'SalaryUpdate']);

Route::post('/stock/update/{id}', [ProductController::class,'StockUpdate']);

Route::Get('/getting/product/{id}', [PosController::class,'GetProduct']);

// Add to cart Route
Route::Get('/addToCart/{id}', [CartController::class,'AddToCart']);
Route::Get('/cart/product',[CartController::class,'CartProduct'] );

Route::Get('/remove/cart/{id}',[CartController::class,'removeCart']);

Route::Get('/increment/{id}', [CartController::class,'increment']);
Route::Get('/decrement/{id}', [CartController::class,'decrement']);