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
        $productsWC = $woocommerce->get('products');
        $utilidad = GlobalAttribute::find(2);
        $categoriesWoocommerce = $woocommerce->get('products/categories');
        $data = ['create' => [], 'update' => []];
        foreach ($products as $product) {
            // Primero agregar las categorias en caso de que no existan
            $categoryProduct =  $product->productCategories[0]->category->slug;
            $categoryAvailable = false;
            foreach ($categoriesWoocommerce as $categoryWC) {
                if ($categoryProduct == $categoryWC->slug) {
                    $categoryAvailable = $categoryWC;
                }
            }

            if ($categoryAvailable === false) {
                $data = [
                    'name' => $product->productCategories[0]->category->family,
                ];
                $categoryAvailable = $woocommerce->post('products/categories', $data);
            }

            // Despues de obtener la categoria, calcular el nuevo precio

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
                'stock' => $product->stock,
                'description' => $product->description,
                'type' => 'simple',
                'regular_price' => $price,
                'categories' => [
                    [
                        'id' =>  $categoryAvailable->id
                    ]
                ],
                'images' => [
                    [
                        'src' =>  $product->images[0]->image_url
                    ]
                ]
            ];
            array_push($data['create'], $dataProduct);
            // print_r($dataProduct);
        }
        echo '<pre>';
        // return;
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
