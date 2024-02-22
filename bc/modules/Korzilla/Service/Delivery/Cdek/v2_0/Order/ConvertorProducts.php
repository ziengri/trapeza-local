<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Order;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;

/**
 * Конвертироует продукты в упаковки
 */
class ConvertorProducts
{
    const PACKAGE_WEIGHT_DEFAULT = 500;
    const PACKAGE_LENGTH_DEFAULT = 30;
    const PACKAGE_WIDTH_DEFAULT = 30;
    const PACKAGE_HEIGHT_DEFAULT = 30;

    /**
     * @param array $products массив продуктов
     * 
     * @return array
     */
    public static function convert($products)
    {

        global $setting;
        switch ($setting['cdek_package_type']) {
            case 'one_package_one_product':
                return self::one_package_one_product($products);
            case 'one_package_one_type_products':
                return self::one_package_one_type_products($products);
            case 'one_package_all_products':
                return self::one_package_all_products($products);
            default:
                throw new \Exception("Отсуствует тип сборки упоковок", 1);

        }
    }

    private static function one_package_one_product($products)
    {
        $packages = [];

        $tools = ToolsAssist::getInstance();

        foreach ($products as $product) {

            for ($i = 0; $i < ($product['count'] ?? 1); $i++) {

                $package = [
                    'weight' => $product['weight'] ?? $tools->getPackageDefaultParameter('weight', self::PACKAGE_WIDTH_DEFAULT),
                    'height' => $product['height'] ?? $tools->getPackageDefaultParameter('height', self::PACKAGE_HEIGHT_DEFAULT),
                    'width' => $product['width'] ?? $tools->getPackageDefaultParameter('width', self::PACKAGE_WEIGHT_DEFAULT),
                    'length' => $product['length'] ?? $tools->getPackageDefaultParameter('length', self::PACKAGE_LENGTH_DEFAULT),
                ];

                $package['number'] = $i . '_' . encodestring($product['key'], 1);  //Номер упаковки

                $package['items'] = [
                    [
                        'name' => $product['name'],
                        'ware_key' => $product['key'],
                        'payment' => [
                            'value' => 0,
                        ],
                        'cost' => $product['price'],
                        'weight' => $package['weight'],
                        'amount' => 1,
                    ]
                ];

                $packages[] = $package;

            }

        }

        return $packages;
    }

    private static function one_package_one_type_products($products)
    {
        $packages = [];

        $tools = ToolsAssist::getInstance();

        foreach ($products as $product) {


            $package = [
                'weight' => ($product['weight'] ?? $tools->getPackageDefaultParameter('weight', self::PACKAGE_WIDTH_DEFAULT)) * $product['count'],
                'height' => ($product['height'] ?? $tools->getPackageDefaultParameter('height', self::PACKAGE_HEIGHT_DEFAULT)) * $product['count'],
                'width' => ($product['width'] ?? $tools->getPackageDefaultParameter('width', self::PACKAGE_WEIGHT_DEFAULT)) * $product['count'],
                'length' => ($product['length'] ?? $tools->getPackageDefaultParameter('length', self::PACKAGE_LENGTH_DEFAULT)) * $product['count'],
            ];

            $package['number'] = encodestring($product['key'], 1);  //Номер упаковки

            $package['items'] = [
                [
                    'name' => $product['name'],
                    'ware_key' => $product['key'],
                    'payment' => [
                        'value' => 0,
                    ],
                    'cost' => $product['price'],
                    'weight' => ($product['weight'] ?? $tools->getPackageDefaultParameter('weight', self::PACKAGE_WIDTH_DEFAULT)),
                    'amount' => $product['count'],
                ]
            ];

            $packages[] = $package;

        }
        
        return $packages;
    }

    private static function one_package_all_products($products)
    {
        $packages = [];

        $tools = ToolsAssist::getInstance();

        $package = [
            'weight' => 0,
            'height' => 0,
            'width' => 0,
            'length' => 0,
            'number' => 0 //Номер упаковки

        ];

        foreach ($products as $product) {

            for ($i = 0; $i < ($product['count'] ?? 1); $i++) {

                $package['weight'] += $product['weight'] ?? $tools->getPackageDefaultParameter('weight', self::PACKAGE_WIDTH_DEFAULT);
                $package['height'] += $product['height'] ?? $tools->getPackageDefaultParameter('height', self::PACKAGE_HEIGHT_DEFAULT);
                $package['width'] += $product['width'] ?? $tools->getPackageDefaultParameter('width', self::PACKAGE_WEIGHT_DEFAULT);
                $package['length'] += $product['length'] ?? $tools->getPackageDefaultParameter('length', self::PACKAGE_LENGTH_DEFAULT);

                $package['items'][] =
                    [
                        'name' => $product['name'],
                        'ware_key' => $product['key'],
                        'payment' => [
                            'value' => 0,
                        ],
                        'cost' => $product['price'],
                        'weight' => ($product['weight'] ?? $tools->getPackageDefaultParameter('weight', self::PACKAGE_WIDTH_DEFAULT)),
                        'amount' => 1,

                    ];

            }

        }

        $packages[] = $package;

        return $packages;
    }
}

