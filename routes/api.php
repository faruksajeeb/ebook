<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Api\OptionGroupController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\PublisherController;

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\SupplierController;

use Illuminate\Support\Facades\Route;

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
    'prefix' => 'auth',

], function ($router) {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

Route::middleware('JWT')->group(function () {
    Route::get('get-categories', [CategoryController::class, 'getCategories']);
    Route::get('get-option-groups', [OptionGroupController::class, 'getOptionGroups']);
    Route::get('get-roles', [RoleController::class, 'getRoles']);
    Route::get('get-permissions', [PermissionController::class, 'getPermissions']);
    Route::get('/user/{id}/permissions', [PermissionController::class, 'getUserPermissions']);

    Route::apiResource('/option-groups', OptionGroupController::class);
    Route::apiResource('/options', OptionController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/sub-categories', SubCategoryController::class);

    Route::apiResource('/users', UserController::class);
    Route::apiResource('/roles', RoleController::class);    
    Route::apiResource('/manage-permission', PermissionController::class);

    Route::apiResource('/authors', AuthorController::class);
    Route::post('/authors/{id}',[AuthorController::class,'update']);

    Route::apiResource('/publishers', PublisherController::class);
    Route::post('/publishers/{id}',[PublisherController::class,'update']);

    Route::apiResource('/customers', CustomerController::class);// Custom PUT Api for edit
    Route::post('/customers/{id}',[CustomerController::class,'update']);

    Route::apiResource('/suppliers', SupplierController::class);
    Route::post('/suppliers/{id}',[SupplierController::class,'update']);
    
    
    Route::apiResource('/manage-employee', EmployeeController::class);
    Route::apiResource('/manage-product', ProductController::class);
    Route::apiResource('/manage-expense', ExpenseController::class);

    Route::get('category-export', [CategoryController::class, 'export'])->name('category.export');
    Route::get('category-export-pdf', [CategoryController::class, 'exportPdf'])->name('category.export.pdf');

    Route::post('/salary/paid/{id}', [SalaryController::class, 'Paid']);
    Route::get('/salary', [SalaryController::class, 'AllSalary']);

    Route::get('/salary/view/{id}', [SalaryController::class, 'ViewSalary']);
    Route::get('/edit/salary/{id}', [SalaryController::class, 'EditSalary']);
    Route::post('/salary/update/{id}', [SalaryController::class, 'SalaryUpdate']);

    Route::post('/stock/update/{id}', [ProductController::class, 'StockUpdate']);

    Route::get('/getting/product/{id}', [PosController::class, 'GetProduct']);

// Add to cart Route
    Route::get('/addToCart/{id}', [CartController::class, 'AddToCart']);
    Route::get('/cart/product', [CartController::class, 'CartProduct']);

    Route::get('/remove/cart/{id}', [CartController::class, 'removeCart']);

    Route::get('/increment/{id}', [CartController::class, 'increment']);
    Route::get('/decrement/{id}', [CartController::class, 'decrement']);

// Vat Route
    Route::get('/vats', [CartController::class, 'Vats']);
    Route::post('/orderdone', [PosController::class, 'OrderDone']);

// Order Route
    Route::get('/orders', [OrderController::class, 'TodayOrder']);

    Route::get('/order/details/{id}', [OrderController::class, 'OrderDetails']);
    Route::get('/order/orderdetails/{id}', [OrderController::class, 'OrderDetailsAll']);

    Route::post('/search/order', [PosController::class, 'SearchOrderDate']);

// Admin Dashboard Route

    Route::get('/today/sell', [DashboardController::class, 'TodaySell']);
    Route::get('/today/income', [DashboardController::class, 'TodayIncome']);
    Route::get('/today/due', [DashboardController::class, 'TodayDue']);
    Route::get('/today/expense', [DashboardController::class, 'TodayExpense']);
    Route::get('/today/stockout', [DashboardController::class, 'Stockout']);
});
