<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class IUSBController extends Controller
{
    public function getStockIUSB()
    {
        // Obtener el archivo de IMPORTACIONES USB
        $fichero = public_path('storage/iusb.csv');
        // Abre el fichero para obtener el contenido existente
        $actual = file_get_contents("https://www.iupromo.mx/importacionesusb.csv");
        // Escribe el contenido al fichero
        file_put_contents($fichero, $actual);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

        $spreadsheet = $reader->load($fichero);

        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        if (!empty($sheetData)) {
            foreach ($sheetData as $data) {
                $product = Product::where('provider_id', 4)->where('sku', $data[4])->first();
                if ($product) {
                    $product->stock = $data[3];
                    $product->save();
                }
            }

            $allProducts = Product::where('provider_id', 4)->get();
            foreach ($sheetData as $product) {
                foreach ($allProducts as $key => $value) {
                    if (($value->sku == $product[4])) {
                        break;
                    }
                }
                unset($allProducts[$key]);
            }

            foreach ($allProducts as  $value) {
                $value->visible = 1;
                $value->save();
            }
        }
    }
}
