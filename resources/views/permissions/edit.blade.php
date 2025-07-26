@extends('layouts.master')

@section('title')
    @lang('titulos.Editar_Permiso')
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
            @lang('titulos.Editar_Permiso')                
        @endslot
    @endcomponent

    <form method="POST" action="{{ route('permissions.update', $permission->id) }}"  role="form" enctype="multipart/form-data">
        {{ method_field('PATCH') }}
        @csrf

        @include('permissions.form')

    </form>

    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:none;" id="delete-form">
        @csrf
        @method('DELETE')        
    </form>
@endsection