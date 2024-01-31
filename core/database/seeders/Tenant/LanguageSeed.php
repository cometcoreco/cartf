<?php

namespace Database\Seeders\Tenant;

use App\Mail\TenantCredentialMail;
use App\Models\Admin;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;

class LanguageSeed extends Seeder
{
    public function run()
    {
        Language::insert([
            [
                'name' => __('English (UK)'),
                'direction' => 0,
                'slug' => 'en_GB',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Arabic'),
                'direction' => 1,
                'slug' => 'ar',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('हिन्दी'),
                'direction' => 0,
                'slug' => 'hi_IN',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Türkçe'),
                'direction' => 0,
                'slug' => 'tr_TR',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Italiano'),
                'direction' => 0,
                'slug' => 'it_IT',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Português'),
                'direction' => 0,
                'slug' => 'pt_PT',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Português do Brasil'),
                'direction' => 0,
                'slug' => 'pt_BR',
                'status' => 1,
                'default' => 0
            ],
            [
                'name' => __('Português de Angola'),
                'direction' => 0,
                'slug' => 'pt_AO',
                'status' => 1,
                'default' => 0
            ]
        ]);

        Language::where('slug', get_static_option_central('default_language') ?? 'en_GB')->update([
            'default' => 1
        ]);
    }
}
