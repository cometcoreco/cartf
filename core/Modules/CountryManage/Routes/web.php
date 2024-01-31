<?php

use Modules\CountryManage\Http\Controllers\Tenant\Admin\AdminUserController;
use Modules\CountryManage\Http\Controllers\Tenant\Admin\CityController;
use Modules\CountryManage\Http\Controllers\Tenant\Admin\ImportController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Modules\CountryManage\Http\Controllers\Tenant\Admin\CountryManageController;
use Modules\CountryManage\Http\Controllers\Tenant\Admin\StateController;
use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;

Route::middleware([
    'web',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
    'auth:admin',
    'tenant_admin_glvar',
    'package_expire',
    'set_lang',
    'tenantAdminPanelMailVerify'
])->prefix('admin-home')->name('tenant.')->group(function () {
    /*----------------------------------------------------------------------------------------------------------------------------
    | BACKEND COUNTRY MANAGE AREA
    |----------------------------------------------------------------------------------------------------------------------------*/
    // tenant.admin.state.by.country
    Route::group(['as' => 'admin.'], function () {
        /*-----------------------------------
            COUNTRY ROUTES
        ------------------------------------*/
        Route::group(['prefix' => 'country', "as" => "country."], function () {
            Route::controller(CountryManageController::class)->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('new', 'store')->name('new');
                Route::post('update', 'update')->name('update');
                Route::post('delete/{item}', 'destroy')->name('delete');
                Route::post('bulk-action', 'bulk_action')->name('bulk.action');
            });
        });

        /*-----------------------------------
                    STATE ROUTES
        ------------------------------------*/
        Route::group(['prefix' => 'state', 'as' => 'state.'], function () {
            Route::controller(StateController::class)->group(function () {
                Route::get('/', 'index')->name('all');
                Route::post('new', 'store')->name('new');
                Route::post('update', 'update')->name('update');
                Route::post('delete/{item}', 'destroy')->name('delete');
                Route::post('bulk-action', 'bulk_action')->name('bulk.action');
                Route::get('country-state', 'getStateByCountry')->name('by.country');
                Route::post('mutliple-country-state', 'getMultipleStateByCountry')->name('by.multiple.country');
            });
        });

        /*-----------------------------------
                    CITY ROUTES
        ------------------------------------*/
        Route::group(['prefix'=>'city', 'as' => 'city.'],function() {
            Route::controller(CityController::class)->group(function () {
                Route::match(['get','post'],'/','all_city')->name('all');
                Route::post('edit-city/{id?}','edit_city')->name('edit');
                Route::post('change-status/{id}','city_status')->name('status');
                Route::post('delete/{id}','delete_city')->name('delete');
                Route::post('bulk-action', 'bulk_action_city')->name('delete.bulk.action');

                Route::get('paginate/data', 'pagination')->name('paginate.data');
                Route::get('search-city', 'search_city')->name('search');
            });
        });

        Route::group(['prefix'=>'settings', 'as' => 'settings.'],function() {
            Route::controller(ImportController::class)->group(function () {
                Route::get('csv/import','import_settings')->name('import.csv.settings');
                Route::post('csv/import','update_import_settings')->name('import.csv.update.settings');
                Route::post('csv/import/database','import_to_database_settings')->name('import.database');
                Route::get('csv/import/cancel','cancel_import_settings')->name('import.cancel');
                Route::get('csv/download','sample_download')->name('csv.download.sample');
            });
        });
    });
});


Route::middleware([
    'web',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
    'set_lang',
])->prefix('admin-home')->name('tenant.admin.')->group(function () {
//todo public routes for user and admin
    Route::controller(AdminUserController::class)->group(function () {
        Route::post('get-state', 'get_country_state')->name('au.state.all');
        Route::post('get-city', 'get_state_city')->name('au.city.all');
    });
});
