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

    // Herdez Auth
    public function loginCustomer(Request $request)
    {
        // $xml = file_get_contents('php://input');
        // $cxml = new SimpleXMLElement($xml);
        $xmlString = '
        <?xml version="1.0" encoding="UTF-8"?>
        <!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.014/cXML.dtd">
        <cXML xml:lang="en-US" payloadID="1591126611.9325364@stg1302app4.int.coupahost.com" timestamp="2020-06-02T14:36:51-05:00">
            <Header>
                <From>
                    <Credential domain="DUNS">
                        <Identity>COUPA1</Identity>
                    </Credential>
                </From>
                <To>
                    <Credential domain="DUNS">
                        <Identity>079928354</Identity>
                    </Credential>
                </To>
                <Sender>
                    <Credential domain="DUNS">
                        <Identity>COUPA1</Identity>
                        <SharedSecret>ALD</SharedSecret>
                    </Credential>
                    <UserAgent>Coupa Procurement 1.0</UserAgent>
                </Sender>
            </Header>
            <Request>
                <PunchOutSetupRequest operation="create">
                    <BuyerCookie>99ea3c4c8cf9f6dc905a6b6772daa0d1</BuyerCookie>
                    <Extrinsic name="FirstName">Mary Anne</Extrinsic>
                    <Extrinsic name="LastName">Krzeminski</Extrinsic>
                    <Extrinsic name="UniqueName">maryanne.krzeminski@coupa.com</Extrinsic>
                    <Extrinsic name="UserEmail">maryanne.krzeminski@coupa.com</Extrinsic>
                    <Extrinsic name="User">maryanne.krzeminski@coupa.com</Extrinsic>
                    <Extrinsic name="BusinessUnit">COUPA</Extrinsic>
                    <BrowserFormPost>
                        <URL>https://mwilczek-demo.coupacloud.com/punchout/checkout?id=2</URL>
                    </BrowserFormPost>
                    <Contact role="endUser">
                        <Name xml:lang="en-US">maryanne.krzeminski@coupa.com</Name>
                        <Email>maryanne.krzeminski@coupa.com</Email>
                    </Contact>
                    <SupplierSetup>
                        <URL>https://uttest.free.beeceptor.com</URL>
                    </SupplierSetup>
                </PunchOutSetupRequest>
            </Request>
        </cXML>';

        /* // Verificar que la solicitud de Coupa sea válida
        if ($cxml->getName() !== 'cXML') {
            // La solicitud no es válida, enviar una respuesta de error
            return '<cXML><Response><Status code="500" text="Invalid request"></Status></Response></cXML>';
        } */
        $xmlDoc = new DOMDocument();
        $xmlDoc->loadXML($xmlString, );
        $punhOutRequest = $xmlDoc->getElementsByTagName('PunchOutSetupRequest');
        foreach ($punhOutRequest as $node) {
            if ($node->hasAttribute("name")) {
                echo "NOXNUIS";
                if ($node->getAttribute("name")) {
                }
            }
            // echo  $name = $node->nodeValue;
        }

        // Enviar la respuesta a Coupa
        header('Content-Type: application/xml');
    }
}
