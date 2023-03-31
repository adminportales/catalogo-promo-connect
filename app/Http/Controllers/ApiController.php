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
use App\Models\User;
use DOMDocument;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class ApiController extends Controller
{
    use AuthenticatesUsers;
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

    // Herdez Auth
    public function loginCustomer(Request $request)
    {
        $user = User::where('email', "adminportales@promolife.com.mx")->first();
        // Auth::login(['email' => "adminportales@promolife.com.mx", 'password' => "password"]);

        if (Auth::attempt(['email' => "adminportales@promolife.com.mx", 'password' => "password"])) {
            Auth::login($user);
            // return auth()->user();
            return redirect('/catalogo');
            dd(1);
        }
        return redirect('/');
        $xml = file_get_contents('php://input');
        $cxml = new SimpleXMLElement($xml);

        // Verificar que la solicitud de Coupa sea válida
        if ($cxml->getName() !== 'cXML') {
            // La solicitud no es válida, enviar una respuesta de error
            return '<cXML><Response><Status code="500" text="Invalid request"></Status></Response></cXML>';
        }
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xml);
        $punhOutRequest = $xmlDoc->getElementsByTagName('PunchOutSetupRequest');
        $parent = $punhOutRequest->item(0);
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                if ($child->hasAttribute('name')) {
                    if ($child->getAttribute('name') == "UserEmail") {
                        $user = User::where('email', $child->nodeValue)->first();
                        if ($user) {
                            Auth::attempt(['email' => "adminportales@promolife.com.mx", 'password' => "password"]);
                            // return $user;
                            // Crea un nuevo DOMDocument
                            $doc = new DOMDocument('1.0', 'UTF-8');

                            // Crear nodo raíz
                            $cxml = $doc->createElement('cXML');
                            $cxml->setAttribute('version', '1.1.007');
                            $cxml->setAttribute('xml:lang', 'en-US');
                            $cxml->setAttribute('payloadID', '200303450803006749@b2b.euro.com');
                            $cxml->setAttribute('timestamp', '2020-06-02T14:36:53-05:00');
                            $doc->appendChild($cxml);

                            // Crear nodo Response
                            $response = $doc->createElement('Response');
                            $cxml->appendChild($response);

                            // Crear nodo Status dentro de Response
                            $status = $doc->createElement('Status');
                            $status->setAttribute('code', '200');
                            $status->setAttribute('text', 'OK');
                            $response->appendChild($status);

                            // Crear nodo PunchOutSetupResponse dentro de Response
                            $punchOutSetupResponse = $doc->createElement('PunchOutSetupResponse');
                            $response->appendChild($punchOutSetupResponse);

                            // Crear nodo StartPage dentro de PunchOutSetupResponse
                            $startPage = $doc->createElement('StartPage');
                            $punchOutSetupResponse->appendChild($startPage);

                            // Crear nodo URL dentro de StartPage
                            $url = $doc->createElement('URL', url("") . '/loginEmail?email=adminportales@promolife.com.mx');
                            $startPage->appendChild($url);

                            // Imprimir el XML
                            $xml = $doc->saveXML(); // $dom es el objeto DOMDocument que has creado antes
                            return response($xml, 200)->header('Content-Type', 'text/xml');
                        }
                    }
                }
            }
        }
        // https://intranet.promolife.lat/loginEmail?email=adminportales@promolife.com.mx&password=rHZAWYmb
        // Enviar la respuesta a Coupa
        return  response('<cXML><Response><Status code="500" text="Invalid request"></Status></Response></cXML>', 500)->header('Content-Type', 'application/xml');
    }
}
