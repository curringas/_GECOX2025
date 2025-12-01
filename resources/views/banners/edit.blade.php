@extends('layouts.master')

@section('title') Editar Banner @endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Backend @endslot
        @slot('title') Editar Banner @endslot
    @endcomponent

    <form method="POST" action="{{ route('banners.store') }}" role="form" enctype="multipart/form-data">
        {{ method_field('PATCH') }}
        @csrf

        @include('banners.form', ['banner' => $banner ?? null, 'categorias' => $categorias ?? \App\Models\Categoria::whereNull('Padre')->get()])

    </form>

    @if(isset($banner))

    <form action="{{ route('banner.destroy', ['id' => $banner->Identificador]) }}" method="POST" style="display:none" id="delete-form">
        @csrf
        @method('DELETE')
        <button  class="btn btn-danger" type="submit" id="delete-button">Eliminar banner</button>

    </form>
    @endif
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/gecox_banners.init.js') }}"></script>
    <script>
        $(function(){ $('.select2').select2({ width: '100%' }); });
    </script>
@endsection