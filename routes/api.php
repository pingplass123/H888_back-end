<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\AuthenticationController::class, 'login']);
Route::post('/create-admin/submit', [App\Http\Controllers\AuthenticationController::class, 'createAdminAccount']);
Route::post('/create-customer/submit', [App\Http\Controllers\AuthenticationController::class, 'createCustomerAccount']);

Route::get('/fetch/admin-accounts', [App\Http\Controllers\AdminAccountController::class, 'getAdminList']);
Route::post('/fetch/customer-accounts', [App\Http\Controllers\CustomerAccountController::class, 'getCustomerList']);

Route::post('/edit/admin-name', [App\Http\Controllers\AdminAccountController::class, 'editAdminAccount']);
Route::post('/edit/customer-name', [App\Http\Controllers\CustomerAccountController::class, 'editCustomerAccount']);

Route::post('/delete/admin-account', [App\Http\Controllers\AdminAccountController::class, 'deleteAdminAccount']);
Route::post('/delete/customer-account', [App\Http\Controllers\CustomerAccountController::class, 'deleteCustomerAccount']);

Route::post('/fetch/rooms-list', [App\Http\Controllers\ChatController::class, 'fetchAdminRooms']);
Route::post('/fetch/chat-history', [App\Http\Controllers\ChatController::class, 'fetchChatHistory']);
Route::post('/store-message', [App\Http\Controllers\ChatController::class, 'storeMessage']);