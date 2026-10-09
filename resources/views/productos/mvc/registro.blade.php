@extends('layouts.app')
@section('contenido')
    @include('crud.formulario', ['modo' => 'crear'])
@endsection