<?php
/*----------------------------------------------------------------------------------------------------------------------------
|                                                      BACKEND ROUTES
|----------------------------------------------------------------------------------------------------------------------------*/
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware;

Route::middleware([
    'web',
    InitializeTenancyByDomainCustomisedMiddleware::class,
    PreventAccessFromCentralDomains::class,
    'tenant_glvar',
    'set_lang'
])->group(function () {
    Route::middleware('package_expire')->controller(\Modules\Blog\Http\Controllers\Tenant\Frontend\BlogController::class)
        ->prefix('blogs')->name('tenant.')->group(function () {
            Route::get('/', 'index')->name('frontend.blog.index');
            Route::get('/search/data', 'blog_search_page')->name('frontend.blog.search');
            Route::get('/{slug}', 'blog_single')->name('frontend.blog.single');
            Route::get('/category/{slug?}', 'category_wise_blog_page')->name('frontend.blog.category');
            Route::get('/tags/{any}', 'tags_wise_blog_page')->name('frontend.blog.tags.page');
            Route::post('/search', 'search_wise_blog_page')->name('frontend.blog.search.page');
            Route::get('blog/autocomplete/search/tag/page', 'auto_complete_search_tag_blogs');
            Route::get('/get/tags', 'get_tags_by_ajax')->name('frontend.get.tags.by.ajax');
            Route::get('/get/blog/by/ajax', 'get_blog_by_ajax')->name('frontend.get.blogs.by.ajax');
            Route::post('/blog/comment/store', 'blog_comment_store')->name('frontend.blog.comment.store');
            Route::post('blog/all/comment', 'load_more_comments')->name('frontend.load.blog.comment.data');
        });
    /*----------------------------------------------------------------------------------------------------------------------------
    |                                                      FRONTEND ROUTES (Tenants)
    |----------------------------------------------------------------------------------------------------------------------------*/
});


