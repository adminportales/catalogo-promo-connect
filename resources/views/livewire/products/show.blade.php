@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @livewire('show-and-edit-product', ['product' => $product])
            </div>
        </div>
    </div>
@endsection
