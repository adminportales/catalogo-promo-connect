@extends('cotizador.template')
@section('content')
    <br>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @livewire('catalogo')
            </div>
        </div>
    </div>
@endsection
