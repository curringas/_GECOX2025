@extends('layouts.master')

@section('title')
    @lang('titulos.Editar_Publicacion')
@endsection

@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}">
@endsection

@section('content')    
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Editar_Publicacion')
        @endslot
    @endcomponent

    <form method="POST" action="{{ route('publicacion.store') }}"  role="form" enctype="multipart/form-data">
        {{ method_field('PATCH') }}
        @csrf

        @include('publicaciones.form')

    </form>
    @if (isset($publicacion))
    <form action="{{ route('publicacion.destroy', ['id' => $publicacion->Identificador]) }}" method="POST" style="display:none;" id="delete-form">
        @csrf
        @method('DELETE')        
    </form> 
    @endif
@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Idioma español del deatepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.es.min.js"></script>
    
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/xpt_categorias.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/pages/xpt_seo.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/palabrea_publicaciones.init.js') }}"></script>

@endsection

@section('script-bottom')

@endsection