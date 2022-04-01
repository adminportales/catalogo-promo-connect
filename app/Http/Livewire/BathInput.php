<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product as ModelProduct;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BathInput extends Component
{
    use WithFileUploads;

    // Datos iniciales
    public $fileLayout, $tipo;

    // Dastos despues del paso 1
    public $rutaArchivo;
    public $archivo;

    // Datos despues del inicio de importacion
    public $productsImporteds =  [];

    public function __construct()
    {
        $this->productsImporteds = [];
    }

    public function render()
    {
        return view('livewire.products.bath-input');
    }

    public function save()
    {
        $this->validate([
            'fileLayout' => 'required', // 1MB Max
            'tipo' => 'required', // 1MB Max
        ]);
        if ($this->tipo == 'create') {
            $this->createProductos();
        } else if ($this->tipo == 'update') {
            $this->updateProductos();
        }
    }

    public function createProductos()
    {
        $path = time() . $this->fileLayout->getClientOriginalName();
        $this->fileLayout->storeAs('public/imports', $path);
        $this->rutaArchivo = public_path('storage/imports/' . $path);
        $this->archivo = $path;

        $documento = IOFactory::load($this->rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        $numeroMayorDeFila = $hojaActual->getHighestRow(); // Numérico

        // Obtener el sku
        $maxSKU = ModelProduct::max('internal_sku');
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
            $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(12, $indiceFila)->getValue()));
            $color = Color::where("slug", $slug)->first();
            if (!$color) {
                $color = Color::create([
                    'color' => ucfirst($hojaActual->getCellByColumnAndRow(12, $indiceFila)->getValue()), 'slug' => $slug,
                ]);
            }

            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(14, $indiceFila)->getValue()));
            $categoria = Category::where("slug", $slug)->first();
            if (!$categoria) {
                $categoria = Category::create([
                    'family' => ucfirst($hojaActual->getCellByColumnAndRow(14, $indiceFila)->getValue()), 'slug' => $slug,
                ]);
            }

            // Verificar si la subcategoria existe y si no registrarla
            $subcategoria = null;
            $slugSub = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow(15, $indiceFila)->getValue()));
            $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

            if (!$subcategoria) {
                $subcategoria = $categoria->subcategories()->create([
                    'subfamily' => ucfirst($hojaActual->getCellByColumnAndRow(15, $indiceFila)->getValue()),
                    'slug' => $slugSub,
                ]);
            }

            $productExist = ModelProduct::where('sku', $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue())->first();
            if (!$productExist) {
                $newProduct = ModelProduct::create([
                    'internal_sku' => "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT),
                    'sku' => $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue(),
                    'sku_parent' => $hojaActual->getCellByColumnAndRow(2, $indiceFila)->getValue(),
                    'name' => $hojaActual->getCellByColumnAndRow(3, $indiceFila)->getValue(),
                    'description' => $hojaActual->getCellByColumnAndRow(4, $indiceFila)->getValue(),
                    'price' => $hojaActual->getCellByColumnAndRow(5, $indiceFila)->getValue(),
                    'stock' => $hojaActual->getCellByColumnAndRow(6, $indiceFila)->getValue(),
                    'producto_promocion' => $hojaActual->getCellByColumnAndRow(7, $indiceFila)->getValue(),
                    'descuento' => $hojaActual->getCellByColumnAndRow(8, $indiceFila)->getValue(),
                    'producto_nuevo' => $hojaActual->getCellByColumnAndRow(9, $indiceFila)->getValue(),
                    'precio_unico' => $hojaActual->getCellByColumnAndRow(10, $indiceFila)->getValue(),
                    'provider_id' => $hojaActual->getCellByColumnAndRow(13, $indiceFila)->getValue(),
                    'type_id' => $hojaActual->getCellByColumnAndRow(11, $indiceFila)->getValue(),
                    'color_id' => $color->id,
                ]);
                $newProduct->images()->create([
                    'image_url' => $hojaActual->getCellByColumnAndRow(16, $indiceFila)->getValue()
                ]);

                /*
                Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                $newProduct->productCategories()->create([
                    'category_id' => $categoria->id,
                    'subcategory_id' => $subcategoria->id,
                ]);
                $idSku++;
                // dd($newProduct);

                $this->productsImporteds = array_push($this->productsImporteds, 1);
            }
            dd($this->productsImporteds);
        }
    }

    public function updateProductos()
    {
        # code...
    }
}
