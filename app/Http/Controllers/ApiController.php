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
use App\Models\Role;
use App\Models\User;
use DOMDocument;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $dataUser = [];
        foreach ($parent->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                if ($child->hasAttribute('name')) {
                    switch ($child->getAttribute('name')) {
                        case 'FirstName':
                            $dataUser['name'] =  $child->nodeValue;
                            break;
                        case 'LastName':
                            $dataUser['lastName'] =  $child->nodeValue;
                            break;
                        case 'UniqueName':
                            $dataUser['uniqueName'] =  $child->nodeValue;
                            break;
                        case 'UserEmail':
                            $dataUser['userEmail'] =  $child->nodeValue;
                            break;
                        case 'User':
                            $dataUser['user'] =  $child->nodeValue;
                            break;
                        case 'BusinessUnit':
                            $dataUser['businessUnit'] =  $child->nodeValue;
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            }
        }
        if (!$this->array_keys_exists(['name', 'lastName', 'uniqueName', "userEmail", "user", "businessUnit"], $dataUser)) {
            return response('<cXML><Response><Status code="500" text="Invalid request"></Status></Response></cXML>', 500)->header('Content-Type', 'application/xml');
        }
        $user = User::firstOrCreate(['email' => $dataUser['userEmail']], [
            'name' => $dataUser['name'] . ' ' . $dataUser['lastName'],
            'password' => Hash::make($dataUser['businessUnit'] . $dataUser['user']),
            "external_company" => $dataUser['businessUnit']
        ]);
        if (!$user->hasRole('invitado')) {
            $admin = Role::where('name', 'invitado')->first();
            $user->attachRole($admin);
        }
        $user->settingsUser()->updateOrCreate(['user_id' => $user->id], ['utility' => 22]);
        if ($user) {
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
            $url = $doc->createElement('URL', url("") . '/loginPunchOut?data=' . $user->email . '#tokenable=dejiomseu839hd398wrtg2373dq');
            $startPage->appendChild($url);

            // Imprimir el XML
            $xml = $doc->saveXML(); // $dom es el objeto DOMDocument que has creado antes
            return response($xml, 200)->header('Content-Type', 'text/xml');
        }
        // https://intranet.promolife.lat/loginEmail?email=adminportales@promolife.com.mx&password=rHZAWYmb
        // Enviar la respuesta a Coupa

    }

    public function loginPunchOut(Request $req)
    {
        $user = User::where('email', $req->data)->first();
        if($user->external_company!== null){
            Auth::login($user);
            return  redirect('/catalogo');
        }
        return  redirect('/login');
    }

    function array_keys_exists(array $keys, array $array): bool
    {
        $diff = array_diff_key(array_flip($keys), $array);
        return count($diff) === 0;
    }
}
