<?php

use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Modules\Integrations\Http\Controllers\IntegrationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* ------------------------------------------
     LANDLORD ADMIN ROUTES
-------------------------------------------- */
Route::group(['middleware' => ['auth:admin','adminglobalVariable', 'set_lang'],'prefix' => 'admin-home'],function () {
    Route::get("integrations-manage",[IntegrationsController::class,"index"])->name("landlord.integration");
    Route::post("integrations-manage",[IntegrationsController::class,"store"]);
    Route::post("integrations-manage/active",[IntegrationsController::class,"activate"])->name('landlord.integration.activation');
});

Route::group(['middleware' => [
    'auth:admin','adminglobalVariable', 'set_lang',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
],'prefix' => 'admin-home/integrations/tenant'],function () {
    Route::get("manage",[IntegrationsController::class,"index"])->name("tenant.integration");
    Route::post("manage",[IntegrationsController::class,"store"]);
    Route::post("manage/active",[IntegrationsController::class,"activate"])->name('tenant.integration.activation');
});
