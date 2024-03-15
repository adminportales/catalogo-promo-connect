<?php

namespace App\Http\Controllers\Providers;

use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DKPSController extends Controller
{
    public function getAllProductsDKSP()
    {
        $url = 'http://intuicion.com.mx/Existencias/DescargarPlantilla?user=C00007&password=P90mo1if3';

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            "$url no es una URL válida";
        }
        $contenido = file_get_contents($url);

        // Guardar el contenido en un archivo local
        Storage::put("public/tmp/Existencia.xlsx", $contenido);
        // Verificar si la descarga fue exitosa
        if ($contenido !== false) {
            echo 'Descarga exitosa. El archivo se guardó como tmp/Existencia.xlsx';
        } else {
            echo 'Error al descargar el archivo';
        }

        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load("storage/tmp/Existencia.xlsx");

        // Seleccionar la hoja de trabajo (puedes cambiar 'Sheet1' al nombre de tu hoja)
        $hoja = $spreadsheet->getSheetByName('Hoja1');
        $totalFilas = $hoja->getHighestRow();
        // Obtener todas las filas como un arreglo asociativo
        $datos = $hoja->rangeToArray('A2:N' . $totalFilas, null, true, true, true);

        $maxSKU = Product::max('internal_sku');
        $idSku = null;
        if (!$maxSKU) {
            $idSku = 1;
        } else {
            $idSku = (int) explode('-', $maxSKU)[1];
            $idSku++;
        }


        foreach ($datos as $file) {


            $color = null;
            $slug = mb_strtolower(str_replace(' ', '-', $file['E']));

            $color = Color::where("slug", $slug)->first();

            if (!$color) {
                $color = Color::create([
                    'color' => ucfirst($file['E']), 'slug' => $slug,
                ]);
            }
            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $file['F']));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($file['F']), 'slug' => $slug,
                ]);
            }

            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = null;
            $slugSub = mb_strtolower(str_replace(' ', '-', $file['F']));
            $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

            if (!$subcategoria) {
                $subcategoria = $categoria->subcategories()->create([
                    'subfamily' => ucfirst($file['F']),
                    'slug' => $slugSub,
                ]);
            }
            $productExist = Product::where('sku', $file['A'])->where('color_id', $color->id)->first();
            $exp = $file['H'];
            $price = (float)$exp;

            if (!$productExist) {
                $newProduct = Product::create([
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku' => 0,
                    'name' => $file['B'],
                    'price' =>  $price,
                    'description' => $file['B'],
                    'stock' => $file['C'],
                    'producto_promocion' => 0,
                    'descuento' => 0,
                    'producto_nuevo' => 0,
                    'precio_unico' => true,
                    'provider_id' => 15,
                    'type_id' => 1,
                    'color_id' => $color->id,
                ]);


                $url_prod =   url("storage/DKSP/" . $nombreImagen = $file['K']);


                $newProduct->images()->create([
                    'image_url' => url($url_prod)
                ]);

                $attributes = [
                    [
                        'attribute' => 'Marca',
                        'slug' => 'marca',
                        'value' => $file['D'],
                    ],
                    [
                        'attribute' => 'Unidad de venta',
                        'slug' => 'unidad de venta',
                        'value' => $file['L'],
                    ],
                    [
                        'attribute' => 'Cantidad por paquete',
                        'slug' => 'cantidad por paquete',
                        'value' => $file['M'],
                    ],
                    [
                        'attribute' => 'Peso',
                        'slug' => 'peso',
                        'value' => $file['N'],
                    ],

                ];
                foreach ($attributes as $attr) {
                    $newProduct->productAttributes()->create($attr);
                }

                $idSku++;
            } else {

                $productExist->update([
                    'price' => $file['H'],
                    'stock' => $file['C'],
                ]);
            }
        }
    }
}
