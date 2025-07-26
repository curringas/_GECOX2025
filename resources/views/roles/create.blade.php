@extends('layouts.master')

@section('title')
    @lang('titulos.Crear_Rol')
@endsection

@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Crear_Rol')
        @endslot
    @endcomponent
    <form method="POST" action="{{ route('roles.store', $role->id) }}"  role="form" enctype="multipart/form-data">
        @csrf

        @include('roles.form')

    </form>
@endsection
