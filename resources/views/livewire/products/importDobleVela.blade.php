@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        @livewire('bath-input-doble-vela')
        {{-- <div class="row">
            <div class="col-md-12">
                <h4>Importar Productos</h4>
                <p>Utiliza el formulario para cada proveedor</p>
                <br>
            </div>
            <div class="col-md-4">
                <h5>Importaciones USB</h5>
                <form action="{{ route('import.iusb') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroupFileAddon01">Layout</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputGroupFile01"
                                aria-describedby="inputGroupFileAddon01" name="layout">
                            <label class="custom-file-label" for="inputGroupFile01">Seleccionar Archivo</label>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-success" value="Enviar">
                </form>
            </div>
        </div> --}}
    </div>
@endsection
