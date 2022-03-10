<?php

namespace App\Http\Controllers;

use Automattic\WooCommerce\Client as WooCommerceClient;

use App\Models\Product;
use Illuminate\Http\Request;


class SendProductsToEcommerce extends Controller
{
    public function setAllProducts()
    {


        $woocommerce = new WooCommerceClient(
            env('STORE_URL', ''), // Your store URL
            env('CONSUMER_KEY', ''), // Your consumer key
            env('CONSUMER_SECRET', ''), // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3' // WooCommerce WP REST API version
            ]
        );
        $products = Product::where('ecommerce', 1)->get();
        $data = ['create' => []];
        foreach ($products as $product) {
            $dataProduct = [
                'name' => $product->name,
                'type' => 'simple',
                'regular_price' => $product->price * 1.76,
                'categories' => [
                    [
                        'id' => 59
                    ]
                ],
                'images' => [
                    [
                        'src' => $product->image
                    ]
                ]
            ];
            array_push($data['create'], $dataProduct);
        }

        echo '<pre>';
        print_r($woocommerce->post('products/batch', $data));
        echo '</pre>';
    }
    public function updateAllProducts()
    {
        # code...
    }
}
