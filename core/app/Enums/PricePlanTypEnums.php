<?php

namespace App\Enums;

use App\Helpers\ModuleMetaData;
use ReflectionClass;

class PricePlanTypEnums
{
    const MONTHLY = 0;
    const YEARLY = 1;
    const LIFETIME = 2;

    public static function getText(int $const)
    {
        foreach (self::getPricePlanTypeList() as $index => $item) {
            if ($const == $index) {
                return __(ucwords(strtolower($item)));
            }
        }
    }

    private static function getAttributes(): array
    {
        $reflect = new ReflectionClass(__CLASS__);
        return $reflect->getConstants() ?? [];
    }

    public static function getPricePlanTypeList(): array
    {
        $valueArr = [];
        foreach (self::getAttributes() as $index => $attribute) {
            $valueArr[$attribute] = __(ucwords(strtolower($index)));
        }

        return $valueArr;
    }

    public static function getFeatureList()
    {
        $all_features = [
            'products' => __('products'),
            'pages' => __('pages'),
            'blog' => __('blog'),
            'storage' => __('storage'),
            'inventory' => __('inventory'),
            'campaign' => __('campaign'),
            'coupon' => __('coupon'),
            'digital_product' => __('digital product'),
            'custom_domain' => __('custom domain'),
            'newsletter' => __('newsletter'),
            'testimonial' => __('testimonial'),
            'app_api' => __('app api')
        ];

        $external_plugins = (new ModuleMetaData())->getExternalPluginsName();
        foreach ($external_plugins ?? [] as $plugin)
        {
            if (array_key_exists('type', $plugin) && $plugin['type'] == 'payment_gateway')
            {
                continue;
            }

            if (array_key_exists('name', $plugin) && array_key_exists('alias', $plugin))
            {
                if (moduleExists($plugin['name']))
                {
                    $all_features[$plugin['alias']] = __($plugin['name']);
                }
            }
        }

        // Todo: remove this woocommerce fallback when the woocommerce plugin will get official update
        if (!array_key_exists('woocommerce', $all_features))
        {
            if (moduleExists('WooCommerce'))
            {
                $all_features['woocommerce'] = __('woocommerce');
            }
        }

        return $all_features;
    }
}
