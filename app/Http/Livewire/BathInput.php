<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BathInput extends Component
{
    use WithFileUploads;

    public $fileLayout, $tipo;
    public $rutaArchivo;
    public $archivo;

    public function __construct()
    {
        $this->columnsToProduct = [
            'nombre' => ''
        ];
    }

    public function render()
    {
        return view('livewire.bath-input.bath-input');
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
        $letraMayorDeColumna = $hojaActual->getHighestColumn(); // Letra
        $numeroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);
        for ($indiceColumna = 1; $indiceColumna <= $numeroMayorDeColumna; $indiceColumna++) {

        }
    }
    public function updateProductos()
    {
        # code...
    }
}
