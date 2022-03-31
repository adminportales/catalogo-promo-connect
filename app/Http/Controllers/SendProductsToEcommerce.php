<?php

namespace App\Http\Controllers;

use App\Models\GlobalAttribute;
use Automattic\WooCommerce\Client as WooCommerceClient;

use App\Models\Site;


class SendProductsToEcommerce extends Controller
{
    public function setProductsToGoodIN()
    {
        // Actualizacion de Woocomerce Para Good IN
        $site = Site::find(1);
        $woocommerce = new WooCommerceClient(
            $site->url, // Your store URL
            $site->consumer_key, // Your consumer key
            $site->consumer_secret, // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'timeout' => 0,
                'version' => 'wc/v3' // WooCommerce WP REST API version
            ]
        );
        $products = $site->sitesProducts;
        $categoriesWoocommerce = $woocommerce->get('products/categories?per_page=100');
        foreach ($products as $product) {
            // Primero agregar las categorias en caso de que no existan
            $categoryProduct = $this->eliminar_tildes($product->productCategories[0]->category->slug);
            $categoryAvailable = false;
            foreach ($categoriesWoocommerce as $categoryWC) {
                if ($categoryProduct == $categoryWC->slug) {
                    $categoryAvailable = $categoryWC;
                }
            }

            if ($categoryAvailable === false) {
                $data = [
                    'name' =>  ucwords(strtolower($this->eliminar_tildes($product->productCategories[0]->category->family))),
                ];
                $categoryAvailable = $woocommerce->post('products/categories', $data);
                $categoriesWoocommerce = $woocommerce->get('products/categories?per_page=100');
            }
        }
        $categoriesWoocommerce = $woocommerce->get('products/categories?per_page=100');
        $productsWC = $woocommerce->get('products?per_page=100');

        $utilidad = $site->utility;
        $utilidadPL = GlobalAttribute::find(1);
        $data = ['create' => [], 'update' => []];
        foreach ($products as $product) {
            // Primero agregar las categorias en caso de que no existan
            $categoryProduct =  $this->eliminar_tildes($product->productCategories[0]->category->slug);
            $categoryAvailable = false;
            foreach ($categoriesWoocommerce as $categoryWC) {
                if ($categoryProduct == $categoryWC->slug) {
                    $categoryAvailable = $categoryWC;
                }
            }

            // Despues de obtener la categoria, calcular el nuevo precio
            $price = null;
            $priceProduct = null;
            if ($product->precio_unico) {
                $priceProduct = $product->price;
            } else {
                $priceProduct = $product->precios[0]->price;
            }

            // Aplicamos descuentos si el producto tiene promocion
            // EL descuento asignado por el proveedor si no tiene promocion
            if ($product->producto_promocion) {
                $priceProduct = round($priceProduct - $priceProduct * ($product->descuento / 100), 2);
            } else {
                $priceProduct = round($priceProduct - $priceProduct * ($product->provider->discount / 100), 2);
            }

            // Agregar Utilidad de Promo Life
            $price = round($priceProduct + $priceProduct * ($utilidadPL->value / 100), 2);

            // Agregar Utilidad de Ecommerce
            $price = round($price + $price * ($utilidad / 100), 2);

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
                    "stock_quantity" => $product->stock,
                    'regular_price' => $price,
                ];
                array_push($data['update'], $dataProduct);
            } else {
                $imgs = [];
                foreach ($product->images as $image) {
                    array_push($imgs, ['src' => $image->image_url]);
                }

                $dataProduct = [
                    'name' => ucwords(strtolower($product->name)),
                    'sku' => $product->internal_sku,
                    "manage_stock" => true,
                    "stock_quantity" => $product->stock,
                    'description' => ucfirst($product->description),
                    'type' => 'simple',
                    'regular_price' => $price,
                    'categories' => [
                        [
                            'id' =>  $categoryAvailable->id
                        ]
                    ],
                    'images' => [
                        [
                            'src' =>  $product->firstImage->image_url
                        ]
                    ]
                ];
                array_push($data['create'], $dataProduct);
            }
        }
        echo '<pre>';
        print_r($data);
        // print_r($woocommerce->post('products/batch', $data));
        echo '</pre>';
    }
    public function eliminar_tildes($cadena)
    {

        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        //$cadena = utf8_encode($cadena);

        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena
        );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena
        );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena
        );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena
        );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );

        return $cadena;
    }
}
