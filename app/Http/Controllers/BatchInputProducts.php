<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BatchInputProducts extends Controller
{
    public function updateProductsIUSB(Request $request)
    {
        request()->validate([
            'layout' => 'required'
        ]);
        $file = $request->file('layout');
        $fileName = 'public/' . $file->getClientOriginalName();
        Storage::disk('local')->put($fileName, File::get($file));

        $contents = public_path('storage/' . $file->getClientOriginalName());

        $documento = IOFactory::load($contents);

        # Obtener hoja en el índice que vaya del ciclo
        $hojaActual = $documento->getSheet(0);
        echo "<h3>Vamos en la hoja con índice 1</h3>";

        # Calcular el máximo valor de la fila como entero, es decir, el
        # límite de nuestro ciclo
        $numeroMayorDeFila = $hojaActual->getHighestRow(); // Numérico

        $maxSKU = Product::max('internal_sku');
        $idSku = null;
        if (!$maxSKU) {
            $idSku = 1;
        } else {
            $idSku = (int) explode('-', $maxSKU)[1];
            $idSku++;
        }

        for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
            // Verificar si el color existe y si no registrarla
            $color = null;
            $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(7, $indiceFila)->getValue()));
            $color = Color::where("slug", $slug)->first();
            if (!$color) {
                $color = Color::create([
                    'color' => ucfirst($hojaActual->getCellByColumnAndRow(7, $indiceFila)->getValue()), 'slug' => $slug,
                ]);
            }
            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(10, $indiceFila)->getValue()));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($hojaActual->getCellByColumnAndRow(10, $indiceFila)->getValue()), 'slug' => $slug,
                ]);
            }

            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = null;
            $slugSub = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(11, $indiceFila)->getValue()));
            $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

            if (!$subcategoria) {
                $subcategoria = $categoria->subcategories()->create([
                    'subfamily' => ucfirst($hojaActual->getCellByColumnAndRow(11, $indiceFila)->getValue()),
                    'slug' => $slugSub,
                ]);
            }


            $productExist = Product::where('sku', $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue())->first();
            if (!$productExist) {
                $newProduct = Product::create([
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku' => $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue(),
                    'name' => $hojaActual->getCellByColumnAndRow(2, $indiceFila)->getValue(),
                    'description' => $hojaActual->getCellByColumnAndRow(3, $indiceFila)->getValue(),
                    'price' => $hojaActual->getCellByColumnAndRow(4, $indiceFila)->getValue(),
                    'stock' => 0,
                    'provider_id' => 4,
                    'type_id' => 1,
                    'color_id' => $color->id,
                ]);
                $newProduct->images()->create([
                    'image_url' => $hojaActual->getCellByColumnAndRow(9, $indiceFila)->getValue()
                ]);

                /*
                TODO: Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);
                $idSku++;
                // dd($newProduct);
            } else {
                $productExist->update([
                    'price' =>  $hojaActual->getCellByColumnAndRow(4, $indiceFila)->getValue()
                ]);
            }
        }

        Storage::delete($fileName);
    }
}
