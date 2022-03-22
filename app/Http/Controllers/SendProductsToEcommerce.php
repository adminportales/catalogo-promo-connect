<?php

namespace App\Http\Controllers;

use App\Models\GlobalAttribute;
use Automattic\WooCommerce\Client as WooCommerceClient;

use App\Models\Product;
use App\Models\Site;
use Illuminate\Http\Request;


class SendProductsToEcommerce extends Controller
{
    public function setAllProducts()
    {
        $site = Site::find(1);
        $woocommerce = new WooCommerceClient(
            $site->url, // Your store URL
            $site->consumer_key, // Your consumer key
            $site->consumer_secret, // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3' // WooCommerce WP REST API version
            ]
        );
        $products = $site->sitesProducts;
        $utilidad = GlobalAttribute::find(2);
        $data = ['create' => []];
        foreach ($products as $product) {
            $price = null;
            if ($product->dinamycPrices->where('site_id', null)->first()) {
                $price = round($product->price - $product->price * ($product->dinamycPrices->where('site_id', null)->first()->amount / 100), 2);
            } else {
                $price = $product->price;
            }
            $price = round($price + $price * ($utilidad->value / 100), 2);
            $dataProduct = [
                'name' => $product->name,
                'sku' => $product->internal_sku,
                'type' => 'simple',
                'regular_price' => $price,
                'categories' => [
                    [
                        'id' => 59
                    ]
                ],
                'images' => [
                    [
                        'src' =>  $product->images[0]->image_url
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
        $site = Site::find(1);
        $woocommerce = new WooCommerceClient(
            $site->url, // Your store URL
            $site->consumer_key, // Your consumer key
            $site->consumer_secret, // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3' // WooCommerce WP REST API version
            ]
        );
        $productsWC = $woocommerce->get('products');
        $utilidad = GlobalAttribute::find(2);
        $data = ['update' => []];
        foreach ($productsWC as $product) {
            $productC = Product::where('sku', $product->sku)->first();
            if ($productC) {
                $price = null;
                if ($productC->dinamycPrices->where('site_id', null)->first()) {
                    $price = round($productC->price - $productC->price * ($productC->dinamycPrices->where('site_id', null)->first()->amount / 100), 2);
                } else {
                    $price = $productC->price;
                }
                $price = round($price + $price * ($utilidad->value / 100), 2);
                $dataProduct = [
                    'id' => $product->id,
                    'regular_price' => $price,
                    'stock' => $price,
                ];
                array_push($data['update'], $dataProduct);
            } else {
                echo $product->sku . ' No encontrado <br>';
            }
        }
        echo '<pre>';
        print_r($woocommerce->put('products/batch', $data));
        // print_r();
        echo '</pre>';
    }
}
