<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product as ModelProduct;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BathInput extends Component
{
    use WithFileUploads;

    // Datos iniciales
    public $fileLayout, $tipo, $updateProducts = false;

    // Dastos despues del paso 1
    public $rutaArchivo;
    public $archivo;

    public $columns;

    // Datos despues del inicio de importacion
    public $SKU_interno, $SKU, $SKU_Padre, $Nombre, $Descripcion, $Precio, $Stock, $Promocion, $Descuento, $Nuevo_Producto, $Precio_Unico, $Tipo, $Color, $Proveedor, $Familia, $SubFamilia, $Imagenes, $Escalas, $Atributos;
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
        ]);
        // Mostrar columnas
        $path = time() . $this->fileLayout->getClientOriginalName();
        $this->fileLayout->storeAs('public/imports', $path);
        $this->rutaArchivo = public_path('storage/imports/' . $path);
        $this->archivo = $path;

        $documento = IOFactory::load($this->rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        $letraMayorDeColumna = $hojaActual->getHighestColumn(); // Numérico
        $numeroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);
        $columns = [];
        for ($indiceCol = 1; $indiceCol <= $numeroMayorDeColumna; $indiceCol++) {
            array_push($columns, [$indiceCol, $hojaActual->getCellByColumnAndRow($indiceCol, 1)->getValue()]);
        }
        $this->columns = $columns;
    }
    public function typeChange()
    {
        if ($this->tipo == 'create') {
            $this->updateProducts = false;
        } else if ($this->tipo == 'update') {
            $this->updateProducts = true;
        }
    }

    public function saveProductos()
    {
        $this->validate([
            'tipo' => 'required'
        ]);
        if ($this->tipo == 'create') {
            $this->createProducts();
        }
        if ($this->tipo == 'update') {
            $this->updateProducts();
        }
        return;
    }
    public function createProducts()
    {
        $this->validate([
            'Nombre' => 'required|integer',
            'Precio_Unico' => 'required|integer',
            'Tipo' => 'required|integer',
            'Proveedor' => 'required|integer',
        ]);

        $documento = IOFactory::load($this->rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        $numeroMayorDeFila = $hojaActual->getHighestRow();

        $productosImportados = [];

        // Obtener el sku
        $maxSKU = ModelProduct::max('internal_sku');
        $idSku = null;
        if (!$maxSKU) {
            $idSku = 1;
        } else {
            $idSku = (int) explode('-', $maxSKU)[1];
            $idSku++;
        }
        // Revision de Datos, para ver si estan completos y correctos
        // for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
        //     if ($this->SKU) {
        //         $hojaActual->getCellByColumnAndRow($this->Color, $indiceFila)->getValue();
        //     }
        // }
        for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
            // Verificar si el color existe y si no registrarla
            $color = null;
            if (trim($this->Color) != '') {
                $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->Color, $indiceFila)->getValue()));
                $color = Color::where("slug", $slug)->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => ucfirst($hojaActual->getCellByColumnAndRow($this->Color, $indiceFila)->getValue()), 'slug' => $slug,
                    ]);
                }
            } else {
                $color = Color::where("slug", 'Sin Color')->first();
                if (!$color) {
                    $color = Color::create([
                        'color' => 'Sin Color',
                        'slug' => 'sin-color',
                    ]);
                }
            }

            // Verificar si la categoria existe y si no registrarla
            $categoria = null;
            if (trim($this->Familia) != '') {
                $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->Familia, $indiceFila)->getValue()));
                $categoria = Category::where("slug", $slug)->first();
                if (!$categoria) {
                    $categoria = Category::create([
                        'family' => ucfirst($hojaActual->getCellByColumnAndRow($this->Familia, $indiceFila)->getValue()), 'slug' => $slug,
                    ]);
                }
            }

            $subcategoria = null;
            if (trim($this->SubFamilia) != '') {
                // Verificar si la subcategoria existe y si no registrarla
                $slugSub = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->SubFamilia, $indiceFila)->getValue()));
                $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

                if (!$subcategoria) {
                    $subcategoria = $categoria->subcategories()->create([
                        'subfamily' => ucfirst($hojaActual->getCellByColumnAndRow($this->SubFamilia, $indiceFila)->getValue()),
                        'slug' => $slugSub,
                    ]);
                }
            }

            $productExist = ModelProduct::where('sku', $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue())->first();
            if (!$productExist) {
                $dataProduct = [];

                $dataProduct['internal_sku'] = "PROM-" . str_pad($idSku, 7, "0", STR_PAD_LEFT);
                if ($this->SKU) {
                    $dataProduct['sku'] =  $hojaActual->getCellByColumnAndRow($this->SKU, $indiceFila)->getValue();
                    // dd($this->SKU_Padre, $this->SKU_interno, $this->SKU, $dataProduct);
                } else {
                    $dataProduct['sku'] = $dataProduct['internal_sku'];
                }
                if ($this->SKU_Padre) {
                    $dataProduct['sku_parent'] = $hojaActual->getCellByColumnAndRow($this->SKU_Padre, $indiceFila)->getValue();
                }
                dd($this->SKU_Padre, $this->SKU_interno, $this->SKU, $dataProduct);
                $dataProduct['name'] = $hojaActual->getCellByColumnAndRow($this->Nombre, $indiceFila)->getValue();
                if ($this->SKU_Padre) {
                    $dataProduct['description'] = $hojaActual->getCellByColumnAndRow($this->Descripcion, $indiceFila)->getValue();
                } else {
                    $dataProduct['description'] = 'Sin Descripcion';
                }
                if ($this->Precio) {
                    $dataProduct['price'] = $hojaActual->getCellByColumnAndRow($this->Precio, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Precio, $indiceFila)->getValue()
                        : 0;
                } else {
                    $dataProduct['price'] = 0;
                }
                if ($this->Stock) {
                    $dataProduct['stock'] = $hojaActual->getCellByColumnAndRow($this->Stock, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Stock, $indiceFila)->getValue()
                        : 0;
                } else {
                    $dataProduct['stock'] = 0;
                }
                if ($this->Promocion) {
                    $dataProduct['producto_promocion'] = $hojaActual->getCellByColumnAndRow($this->Promocion, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Promocion, $indiceFila)->getValue()
                        : 0;
                } else {
                    $dataProduct['producto_promocion'] = 0;
                }
                if ($this->Descuento) {
                    $dataProduct['descuento'] = $hojaActual->getCellByColumnAndRow($this->Descuento, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Descuento, $indiceFila)->getValue()
                        : 0.00;
                } else {
                    $dataProduct['descuento'] = 0;
                }
                if ($this->Nuevo_Producto) {
                    $dataProduct['producto_nuevo'] = $hojaActual->getCellByColumnAndRow($this->Nuevo_Producto, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Nuevo_Producto, $indiceFila)->getValue()
                        : 0;
                } else {
                    $dataProduct['producto_nuevo'] = 0;
                }
                if ($this->Precio_Unico) {
                    $dataProduct['precio_unico'] = $hojaActual->getCellByColumnAndRow($this->Precio_Unico, $indiceFila)->getValue() != null
                        ? $hojaActual->getCellByColumnAndRow($this->Precio_Unico, $indiceFila)->getValue()
                        : 0;
                } else {
                    $dataProduct['precio_unico'] = 0;
                }

                $dataProduct['provider_id'] = $hojaActual->getCellByColumnAndRow($this->Proveedor, $indiceFila)->getValue();
                $dataProduct['type_id'] = $hojaActual->getCellByColumnAndRow($this->Tipo, $indiceFila)->getValue();

                $dataProduct['color_id'] = $color ? $color->id : null;
                dd($dataProduct);
                $newProduct = ModelProduct::create($dataProduct);

                // Imagenes
                if ($this->Imagenes) {
                    foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Imagenes, $indiceFila)->getValue()) as $img) {
                        $newProduct->images()->create([
                            'image_url' => $img
                        ]);
                    }
                }
                // Escalas
                if (!$newProduct->precio_unico) {
                    if ($this->Escalas) {
                        foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Escalas, $indiceFila)->getValue()) as $esc) {
                            $dataEscala = explode(':', $esc);
                            $newProduct->precios()->create([
                                'escala' => $dataEscala[0],
                                'precio' => $dataEscala[1],
                            ]);
                        }
                    }
                }

                if ($this->Atributos) {
                    if ($hojaActual->getCellByColumnAndRow($this->Atributos, $indiceFila)->getValue() != "") {
                        foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Atributos, $indiceFila)->getValue()) as $att) {
                            $dataAttr = explode(':', $att);
                            if (count($dataAttr) > 0) {
                                $newProduct->productAttributes()->create([
                                    'attribute' => trim($dataAttr[0]),
                                    'slug' => $slug = mb_strtolower(str_replace(' ', '-', trim($dataAttr[0]))),
                                    'value' => $dataAttr[1],
                                ]);
                            }
                        }
                    }
                }
                /*
                Registrar en la tabla product_category el producto, categoria y sub categoria
                */
                if ($categoria != null) {
                    $newProduct->productCategories()->create([
                        'category_id' => $categoria->id,
                        'subcategory_id' => $subcategoria->id,
                    ]);
                }
                $idSku++;
                array_push($productosImportados, $newProduct);
            }
        }
    }
    public function updateProducts()
    {
        $this->validate([
            'SKU_interno' => 'required'
        ]);
    }
    // public function updateProductos()
    // {
    //     if ($this->SKU == trim('') && $this->SKU_interno == trim('')) {
    //         return;
    //     }

    //     $documento = IOFactory::load($this->rutaArchivo);
    //     $hojaActual = $documento->getSheet(0);
    //     $numeroMayorDeFila = $hojaActual->getHighestRow(); // Numérico

    //     $productosImportados = [];

    //     for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
    //         // Verificar si el color existe y si no registrarla
    //         $color = null;
    //         if (trim($this->Color)) {
    //             $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->Color, $indiceFila)->getValue()));
    //             $color = Color::where("slug", $slug)->first();
    //             if (!$color) {
    //                 $color = Color::create([
    //                     'color' => ucfirst($hojaActual->getCellByColumnAndRow($this->Color, $indiceFila)->getValue()), 'slug' => $slug,
    //                 ]);
    //             }
    //         }

    //         // Verificar si la categoria existe y si no registrarla
    //         $categoria = null;
    //         if (trim($this->Familia)) {
    //             $slug = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->Familia, $indiceFila)->getValue()));
    //             $categoria = Category::where("slug", $slug)->first();
    //             if (!$categoria) {
    //                 $categoria = Category::create([
    //                     'family' => ucfirst($hojaActual->getCellByColumnAndRow($this->Familia, $indiceFila)->getValue()), 'slug' => $slug,
    //                 ]);
    //             }
    //         }

    //         $subcategoria = null;
    //         if (trim($this->SubFamilia)) {
    //             // Verificar si la subcategoria existe y si no registrarla
    //             $slugSub = mb_strtolower(str_replace(' ', '-', $hojaActual->getCellByColumnAndRow($this->SubFamilia, $indiceFila)->getValue()));
    //             $subcategoria = $categoria->subcategories()->where("slug", $slugSub)->first();

    //             if (!$subcategoria) {
    //                 $subcategoria = $categoria->subcategories()->create([
    //                     'subfamily' => ucfirst($hojaActual->getCellByColumnAndRow($this->SubFamilia, $indiceFila)->getValue()),
    //                     'slug' => $slugSub,
    //                 ]);
    //             }
    //         }
    //         $productExist = '';
    //         if ($this->SKU_interno != trim('')) {
    //             $productExist = ModelProduct::where('sku', trim($hojaActual->getCellByColumnAndRow($this->SKU_interno, $indiceFila)->getValue()))->first();
    //         } else if ($this->SKU == trim('')) {
    //             $productExist = ModelProduct::where('sku', trim($hojaActual->getCellByColumnAndRow($this->SKU_interno, $indiceFila)->getValue()))->first();
    //         } else {
    //             $productExist = ModelProduct::where('sku', trim($hojaActual->getCellByColumnAndRow($this->SKU, $indiceFila)->getValue()))->first();
    //         }
    //         if ($productExist) {
    //             $dataProduct = [];

    //             if ($this->SKU) {
    //                 $dataProduct['sku'] =  $hojaActual->getCellByColumnAndRow($this->SKU, $indiceFila)->getValue();
    //             }
    //             if ($this->SKU_Padre) {
    //                 $dataProduct['sku_parent'] = $hojaActual->getCellByColumnAndRow($this->SKU_Padre, $indiceFila)->getValue();
    //             }
    //             if ($this->Nombre) {
    //                 $dataProduct['name'] = $hojaActual->getCellByColumnAndRow($this->Nombre, $indiceFila)->getValue();
    //             }
    //             if ($this->Descripcion) {
    //                 $dataProduct['description'] = $hojaActual->getCellByColumnAndRow($this->Descripcion, $indiceFila)->getValue();
    //             }
    //             if ($this->Precio) {
    //                 $dataProduct['price'] = $hojaActual->getCellByColumnAndRow($this->Precio, $indiceFila)->getValue();
    //             }
    //             if ($this->Stock) {
    //                 $dataProduct['stock'] = $hojaActual->getCellByColumnAndRow($this->Stock, $indiceFila)->getValue();
    //             }
    //             if ($this->Promocion) {
    //                 $dataProduct['producto_promocion'] = $hojaActual->getCellByColumnAndRow($this->Promocion, $indiceFila)->getValue();
    //             }
    //             if ($this->Descuento) {
    //                 $dataProduct['descuento'] = $hojaActual->getCellByColumnAndRow($this->Descuento, $indiceFila)->getValue();
    //             }
    //             if ($this->Nuevo_Producto) {
    //                 $dataProduct['producto_nuevo'] = $hojaActual->getCellByColumnAndRow($this->Nuevo_Producto, $indiceFila)->getValue();
    //             }
    //             if ($this->Precio_Unico) {
    //                 $dataProduct['precio_unico'] = $hojaActual->getCellByColumnAndRow($this->Precio_Unico, $indiceFila)->getValue();
    //             }
    //             if ($this->Proveedor) {
    //                 $dataProduct['provider_id'] = $hojaActual->getCellByColumnAndRow($this->Proveedor, $indiceFila)->getValue();
    //             }
    //             if ($this->Tipo) {
    //                 $dataProduct['type_id'] = $hojaActual->getCellByColumnAndRow($this->Tipo, $indiceFila)->getValue();
    //             }
    //             if ($this->Color) {
    //                 $dataProduct['color_id'] = $color ? $color->id : null;
    //             }

    //             $productExist->update($dataProduct);
    //             if ($this->Imagenes) {
    //                 foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Imagenes, $indiceFila)->getValue()) as $img) {
    //                     $productExist->images()->delete();
    //                     $productExist->images()->create([
    //                         'image_url' => $img
    //                     ]);
    //                 }
    //             }

    //             if (!$productExist->precio_unico) {
    //                 if ($this->Escalas) {
    //                     foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Escalas, $indiceFila)->getValue()) as $esc) {
    //                         $dataEscala = explode(':', $esc);
    //                         $productExist->precios()->delete();
    //                         $productExist->precios()->create([
    //                             'escala' => $dataEscala[0],
    //                             'precio' => $dataEscala[1],
    //                         ]);
    //                     }
    //                 }
    //             }

    //             if ($this->Atributos) {
    //                 if ($hojaActual->getCellByColumnAndRow($this->Atributos, $indiceFila)->getValue() != "") {
    //                     foreach (explode(',', $hojaActual->getCellByColumnAndRow($this->Atributos, $indiceFila)->getValue()) as $att) {
    //                         $dataAttr = explode(':', $att);
    //                         $productExist->productAttributes()->delete();
    //                         $productExist->productAttributes()->create([
    //                             'attribute' => trim($dataAttr[0]),
    //                             'slug' => $slug = mb_strtolower(str_replace(' ', '-', trim($dataAttr[0]))),
    //                             'value' => $dataAttr[1],
    //                         ]);
    //                     }
    //                 }
    //             }


    //             /*
    //             Registrar en la tabla product_category el producto, categoria y sub categoria
    //             */
    //             if ($this->Familia) {
    //                 $productExist->productCategories()->create([
    //                     'category_id' => $categoria->id,
    //                 ]);
    //             }
    //             if ($this->SubFamilia) {
    //                 $productExist->productCategories()->create([
    //                     'subcategory_id' => $subcategoria->id,
    //                 ]);
    //             }
    //             array_push($productosImportados, $productExist);
    //         }
    //     }
    //     $this->productsImporteds = $productosImportados;
    // }

    public function firstStep()
    {
        $this->archivo = null;
    }
}
