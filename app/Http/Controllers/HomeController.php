<?php

namespace App\Http\Controllers;

use App\Models\FailedJobsCron;
use App\Models\Product;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->action([HomeController::class, 'dashboard']);
        } else {
            return redirect('catalogo');
        }
    }

    public function dashboard()
    {
        $erroresPV = FailedJobsCron::where('type', 1)->get();
        $erroresWC = FailedJobsCron::where('type', 2)->get();
        return view('home', compact('erroresPV', 'erroresWC'));
    }
    public function catalogo()
    {
        return view('cotizador.catalogo');
    }

    public function obtenerProductos()
    {
        // Consulta de Productos
        $productos = Product::where('visible', 1)
            ->where('type_id', 1)
            ->whereIn('provider_id', [1, 2, 3])
            ->orderBy('provider_id', 'asc')
            ->limit(100)
            ->get();

        // Creación del documento
        $documento = new Spreadsheet();
        $documento
            ->getProperties()
            ->setCreator("Aquí va el creador, como cadena")
            ->setLastModifiedBy('Parzibyte') // última vez modificado por
            ->setTitle('Mi primer documento creado con PhpSpreadSheet')
            ->setSubject('El asunto')
            ->setDescription('Este documento fue generado para parzibyte.me')
            ->setKeywords('etiquetas o palabras clave separadas por espacios')
            ->setCategory('La categoría');

        $nombreDelDocumento = "Catalogo de Productos " . now()->format('d-m-Y') . ".xlsx";
        $hoja = $documento->getActiveSheet();
        $hoja->setTitle("Catalogo de Productos");
        $i = 2;
        $hoja->setCellValue([1, 1], '#');
        $hoja->setCellValue([2, 1], 'SKU Interno');
        $hoja->setCellValue([3, 1], 'Sku Padre');
        $hoja->setCellValue([4, 1], 'Sku Hijo');
        $hoja->setCellValue([5, 1], 'Imagen');
        $hoja->setCellValue([6, 1], 'Nombre');
        $hoja->setCellValue([7, 1], 'Descripcion');
        $hoja->setCellValue([8, 1], 'Disponible');
        $hoja->setCellValue([9, 1], 'Costo');
        $hoja->setCellValue([10, 1], 'Proveedor');
        $hoja->setCellValue([11, 1], 'Familia');
        $hoja->setCellValue([12, 1], 'Subfamilia');

        foreach ($productos as $product) {
            $priceProduct = $product->precio_unico ? $product->price : $product->precios[0]->price;
            if ($product->producto_promocion) {
                $priceProduct = round($priceProduct - $priceProduct * ($product->descuento / 100), 2);
            } else {
                $priceProduct = round($priceProduct - $priceProduct * ($product->provider->discount / 100), 2);
            }
            $hoja->setCellValue([1, $i], $i - 1);
            $hoja->setCellValue([2, $i], $product->internal_sku);
            $hoja->setCellValue([3, $i], $product->sku_parent);
            $hoja->setCellValue([4, $i], $product->sku);
            // Convertir la celda en hipervínculo con setcellvalue
            $hoja->setCellValue([5, $i], $product->firstImage ? $product->firstImage->image_url : 'Sin Imagen');
            if ($product->firstImage)
                $hoja->getCell([5, $i])->getHyperlink()->setUrl($product->firstImage->image_url);
            $hoja->setCellValue([6, $i], $product->name);
            $hoja->setCellValue([7, $i], $product->description);
            $hoja->setCellValue([8, $i], $product->stock);
            $hoja->setCellValue([9, $i], $priceProduct);
            $hoja->setCellValue([10, $i], $product->provider->company);
            $hoja->setCellValue([11, $i], count($product->productCategories) ?  $product->productCategories[0]->category->family : 'Sin Familia');
            $hoja->setCellValue([12, $i], count($product->productCategories) ?  $product->productCategories[0]->category->subcategory : 'Sin SubFamilia');
            // $hoja->setCellValue([12, $i], $product->subcategory->name);

            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($documento, 'Xlsx');
        $writer->save('php://output');
        exit;


        /*    foreach ($users as $user) {
            $hoja->setCellValueByColumnAndRow(1, $i,  $user->name);
            $hoja->setCellValueByColumnAndRow(2, $i,  $user->lastname);
            $hoja->setCellValueByColumnAndRow(3, $i,  $user->email);
            $hoja->setCellValueByColumnAndRow(4, $i,  $user->last_login != null ? $user->last_login : "No hay Registro");
            $i++;
        } */

        /**
         * Los siguientes encabezados son necesarios para que
         * el navegador entienda que no le estamos mandando
         * simple HTML
         * Por cierto: no hagas ningún echo ni cosas de esas; es decir, no imprimas nada
         */

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($documento, 'Xlsx');
        $writer->save('php://output');
        exit;
        // return $productos;
    }
}
