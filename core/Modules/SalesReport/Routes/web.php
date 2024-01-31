<?php

use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Modules\SalesReport\Http\Controllers\Tenant\SalesReportController;

Route::middleware([
    'web',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
    'auth:admin',
    'tenant_admin_glvar',
    'package_expire',
    'tenantAdminPanelMailVerify',
    'tenant_status',
    'set_lang'
])->prefix('admin-home')->name('tenant.')->group(function () {
    Route::controller(SalesReportController::class)->prefix('sales-report')->name('admin.sales.')->group(function (){
        Route::get('/', 'index')->name('dashboard');
        Route::get('/weekly', 'dynamic_report')->name('report.weekly');
        Route::get('/monthly', 'dynamic_report')->name('report.monthly');
        Route::get('/yearly', 'dynamic_report')->name('report.yearly');
        Route::get('/settings', 'settings')->name('settings');
        Route::post('/settings', 'settings_update');
    });
});
