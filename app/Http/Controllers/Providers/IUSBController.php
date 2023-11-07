<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Status;
use Exception;
use Illuminate\Http\Request;

class IUSBController extends Controller
{
    public function getStockIUSB()
    {
        try {
            // Obtener el archivo de IMPORTACIONES USB
            $fichero = public_path('storage/iusb.csv');
            // Abre el fichero para obtener el contenido existente
            $actual = file_get_contents("https://www.iupromo.mx/importacionesusb.csv");
            // Escribe el contenido al fichero
            file_put_contents($fichero, $actual);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'IUSB',
                'status' => 'Problemas al acceder al servidor',
                'update_sumary' => 'No se pudo acceder al servidor de IUSB',
            ]);

            return ('Error al acceder al servidor de IUSB');
        }

        try {
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
                    $value->visible = 0;
                    $value->save();
                }
            }

            /* Status::create([
                'name_provider' => 'IUSB',
                'status' => 'Actualizacion Completa al servidor',
                'update_sumary' => 'Actualizacion Completa de los productos de IUSB',
            ]); */
        } catch (Exception $e) {
            Status::create([
                'name_provider' => 'IUSB',
                'status' => 'Actualización incompleta al servidor',
                'update_sumary' => 'Actualización incompleta de stock del servidor de IUSB',
            ]);

            return ('Actualización incompleta de stock del servidor IUSB');
        }
    }
}
