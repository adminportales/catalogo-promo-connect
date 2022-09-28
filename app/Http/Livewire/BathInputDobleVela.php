<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BathInputDobleVela extends Component
{
    use WithFileUploads;
    public $rutaArchivo;
    public $archivo;
    public $fileLayout;
    public $importacionCorrecta = false;

    public $productsImporteds =  [];

    public function render()
    {
        return view('livewire.products.bath-input-doble-vela');
    }
    public function save()
    {
        $this->validate([
            'fileLayout' => 'required', // 1MB Max
        ]);

        $path = time() . $this->fileLayout->getClientOriginalName();
        $this->fileLayout->storeAs('public/imports', $path);
        $this->rutaArchivo = public_path('storage/imports/' . $path);
        $this->archivo = $path;

        $documento = IOFactory::load($this->rutaArchivo);
        $hojaActual = $documento->getSheet(0);
        $numeroMayorDeFila = $hojaActual->getHighestRow();
        for ($indiceFila = 2; $indiceFila <= $numeroMayorDeFila; $indiceFila++) {
            $sku =  $hojaActual->getCellByColumnAndRow(1, $indiceFila)->getValue();
            $imagen =  $hojaActual->getCellByColumnAndRow(2, $indiceFila)->getValue();
            $product = Product::where('sku', $sku)->where('provider_id', 5)->first();
            if ($product) {
                $product->images()->delete();
                $product->images()->create([
                    'image_url' => $imagen
                ]);
            }
        }
        $this->importacionCorrecta = true;
    }
}
