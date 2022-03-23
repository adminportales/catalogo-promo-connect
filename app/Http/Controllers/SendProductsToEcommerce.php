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
        // Actualizacion de Woocomerce Para Good IN
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
        $categoriesWoocommerce = $woocommerce->get('products/categories');
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
                $categoriesWoocommerce = $woocommerce->get('products/categories');
            }
        }
        $categoriesWoocommerce = $woocommerce->get('products/categories');
        $productsWC = $woocommerce->get('products');

        $utilidad = GlobalAttribute::find(2);
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

            // Despues de obtener la categoria, calcular el nuevo precio
            $price = null;
            if ($product->dinamycPrices->where('site_id', null)->first()) {
                $price = round($product->price - $product->price * ($product->dinamycPrices->where('site_id', null)->first()->amount / 100), 2);
            } else {
                $price = $product->price;
            }
            $price = round($price + $price * ($utilidad->value / 100), 2);

            // Revisar si el producto ya existe en Woocomerce
            $productAvailable = false;
            foreach ($productsWC as $productWC) {
                if ($productWC->sku == $product->internal_sku) {
                    $productAvailable = $productWC;
                }
            }

            if ($productAvailable !== false) {
                $dataProduct = [
                    'id' => $productAvailable->id,
                    'stock' => $product->stock,
                    'regular_price' => $price,
                ];
                array_push($data['update'], $dataProduct);
            } else {
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
            }
        }
        echo '<pre>';
        print_r($woocommerce->post('products/batch', $data));
        echo '</pre>';
    }
}
