@extends('layouts.app')
@section('content')
    @role('admin')
        <a href="{{ url('admin/') }}">Ir al administrador</a>
    @endrole
@endsection
