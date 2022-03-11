@extends('errors.illustrated-layout')

@section('title', __('No autorizado'))
@section('code', '401')
@section('message', __('No tienes autorizacion para acceder a este contenido'))
