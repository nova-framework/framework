@extends('Bootstrap::Layouts/Default')

@section('header')
    @include('Bootstrap::Layouts/Partials/Navbar')
@stop

@section('content')
    @include('Bootstrap::Layouts/Partials/Logo')

    @parent
@stop

@section('footer')
    @include('Bootstrap::Layouts/Partials/Footer')
@stop
