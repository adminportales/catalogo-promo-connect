<?php

namespace App\Http\Controllers\Providers;

use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DKPSController extends Controller
{
    public function getAllProductsDKSP()
    {
        $url = 'http://intuicion.com.mx/Existencias/DescargarPlantilla?user=C00007&password=P90mo1if3';
        $archivoLocal = 'Existencias_C00007.xlsx';

        // Obtener el contenido del archivo desde la URL
        $contenido = file_get_contents($url);
        //dd($contenido);
        // Guardar el contenido en un archivo local
        file_put_contents($archivoLocal, $contenido);

        // Verificar si la descarga fue exitosa
        if ($contenido !== false) {
            echo 'Descarga exitosa. El archivo se guardó como ' . $archivoLocal;
        } else {
            echo 'Error al descargar el archivo';
        }
        // Cargar el archivo Excel
        $spreadsheet = IOFactory::load($archivoLocal);

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
            if (!$productExist) {
                $newProduct = Product::create([
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku' => $file['A'],
                    'name' => $file['B'],
                    'price' =>  $file['H'],
                    'description' => $file['B'],
                    'stock' => $file['C'],
                    'producto_promocion' => null,
                    'descuento' => 0,
                    'producto_nuevo' => 0,
                    'precio_unico' => true,
                    'provider_id' => 1,
                    'type_id' => 1,
                    'color_id' => $color->id,
                ]);
                dd($newProduct);
                foreach (array_reverse($file['K']) as $key => $imagen) {
                    // Descargar Imagenes solo Si no existen
                    $errorGetImage = false;
                    $fileImage = "";
                    try {
                        $fileImage = file_get_contents(str_replace(' ', '%20', $imagen['url_imagen']), false, stream_context_create($arrContextOptions));
                    } catch (Exception $th) {
                        $errorGetImage = true;
                    }
                    $newPath = '';
                    if (!$errorGetImage) {
                        $newPath = '/newimage/' . $newProduct->sku . 'type' . $key . $color->slug . ' ' . $product['nombre_articulo'] . '.jpg';
                        Storage::append('public' . $newPath, $fileImage);
                        $newProduct->images()->create([
                            'image_url' => url('/storage' . $newPath)
                        ]);
                    } else {
                        $newProduct->images()->create([
                            'image_url' => 'img/default_product_image.jpg'
                        ]);
                    }
                }
                /*
                Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);


                // dd($newProduct);
            } else {
                $productExist->update([
                    'price' => $file['H'],
                    'stock' => $file['C'],
                    'producto_promocion' =>0,
                    'descuento' => 0,
                ]);
                if (count($productExist->images) <= 0) {
                    foreach (array_reverse($file['imagenes']) as $key => $imagen) {
                        $errorGetImage = false;
                        $fileImage = "";
                        try {
                            $fileImage = file_get_contents(str_replace(' ', '%20', $imagen['url_imagen']), false, stream_context_create($arrContextOptions));
                        } catch (Exception $th) {
                            $errorGetImage = true;
                        }
                        $newPath = '';
                        if (!$errorGetImage) {
                            $newPath = '/nuevaimagen/' . $productExist->sku . 'type' . $key . $color->slug . ' ' . $product['nombre_articulo'] . '.jpg';
                            Storage::append('public' . $newPath, $fileImage);
                            $productExist->images()->create([
                                'image_url' => url('/storage' . $newPath)
                            ]);
                        } else {
                            $productExist->images()->create([
                                'image_url' => 'img/default_product_image.jpg'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
