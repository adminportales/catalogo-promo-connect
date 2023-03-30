<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\GlobalAttribute;
use App\Models\Image;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCategory;
use App\Models\Provider;
use App\Models\Subcategory;
use App\Models\Type;

use App\Models\FailedJobsCron;
use DOMDocument;
use Illuminate\Http\Request;
use SimpleXMLElement;

class ApiController extends Controller
{
    public function getAllProducts()
    {
        $providers = Provider::all();
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $types = Type::all();
        $colors = Color::all();
        $products = Product::all();
        $productCategory = ProductCategory::all();
        $productAttribute = ProductAttribute::all();
        $images = Image::all();
        $prices = Price::all();
        $globalAttribute = GlobalAttribute::all();
        return response()->json([
            'providers' => $providers,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'types' => $types,
            'colors' => $colors,
            'products' => $products,
            'productCategory' => $productCategory,
            'productAttribute' => $productAttribute,
            'images' => $images,
            'prices' => $prices,
            'globalAttribute' => $globalAttribute,
        ], 200);
    }

    public function getPricePromoOpcion()
    {
        $products = Product::where('provider_id', 2)->get();
        return response()->json($products);
    }

    public function loginCustomer(Request $request)
    {
        $xml = file_get_contents('php://input');
        $cxml = new SimpleXMLElement($xml);

        // Verificar que la solicitud de Coupa sea válida
        if ($cxml->getName() !== 'cXML') {
            // La solicitud no es válida, enviar una respuesta de error
            return $response = '<cXML><Response><Status code="500" text="Invalid request"></Status></Response></cXML>';
        }
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);
        $punhOutRequest = $xmlDoc->getElementsByTagName('PunchOutSetupRequest');
        foreach ($punhOutRequest as $node) {
            $name = $node->nodeValue;
            echo $name;
        }
        /* foreach ($punhOutRequest as $book) {
          $bookId = $book->getAttribute('id');
          if ($bookId == 'bk101') {
            $title = $book->getElementsByTagName('title')->item(0)->nodeValue;
            echo 'El título del libro con id="bk101" es: ' . $title;
          }
        } */

        // Enviar la respuesta a Coupa
        header('Content-Type: application/xml');
        return $response;
    }
}
