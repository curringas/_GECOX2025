@extends('layouts.master')

@section('title') @lang('translation.Dashboards') @endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}">
@endsection

@section('content')

@component('components.breadcrumb')
@slot('li_1') Portada @endslot
@slot('title') Portada @endslot
@endcomponent

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header position-relative">
                <h4 class="card-title">Banner Sobrecabecera</h4>
                <button type="button" 
                        data-tabla="portada_slider" 
                        data-banner="Banner"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary position-absolute top-0 end-0 m-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-plus"></i> Nuevo banner
                </button>
            </div>
            <div class="card-body lista-ordenable" data-tabla="portada_slider" id="listaOrdenableBannersSobrecabecera" style="overflow:hidden;">

                @foreach($sliders as $slider)
                    <div class="mb-4 position-relative" data-id="{{ $slider->Identificador }}">
                        <!-- Botón editar arriba a la derecha -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                            
                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-banner="Banner"  
                                    data-id="{{ $slider->Identificador }}" 
                                    data-orden="{{ $slider->Orden }}" 
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#banner">
                                <i class="mdi mdi-pencil"></i> Editar
                            </button>

                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-eliminar="Banner" 
                                    data-id="{{ $slider->Identificador }}" 
                                    data-orden="{{ $slider->Orden }}" 
                                    class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                            @if ($slider->BannerCodigoFuente)
                                {{ $slider->BannerCodigoFuente }}
                            @elseif (isset($slider) && $slider->BannerImagen)
                                <img src="{{ asset('storage/'.$slider->BannerImagen) }}" 
                                    alt="{{ $slider->BannerTitulo}}"
                                    title="{{ $slider->BannerTitulo}}" 
                                    width="{{ config('gecox_portada.banners.slider.ancho', '1080') }}"
                                    height="{{ config('gecox_portada.banners.slider.alto', '150') }}"
                                    class="img-fluid">
                            @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4 d-flex">
        <div class="card overflow-hidden p-2 text-center w-100">
            <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" class="img-fluid" width="291" height="90">
        </div>
    </div>
    <div class="col-xl-8 d-flex">
        @include('portada.banner',['tabla'=>'portada',
            'banner'=>'banner_cabecera',
            'codigo'=>$portada->banner_cabeceraCodigoFuente,
            'imagen' => $portada->banner_cabeceraImagen,
            'titulo' => $portada->banner_cabeceraTitulo,
            'width'=>config('gecox_portada.banners.cabecera.ancho', '728'),
            'height'=>config('gecox_portada.banners.cabecera.alto', '90')]
        )
    </div>
</div>

<!--MENU DE NAVEGACION -->
<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card bg-dark text-white">
            <div class="card-body">
                Esta es la zona de navegacion 
                    <button type="button" class="btn btn-secondary btn-sm ms-2">
                        <a href="{{ route('categorias.index') }}" class="text-white">
                            Configurar Menu
                        </a>
                    </button>
            </div>
        </div>
    </div>
</div>


<!-- CONTENIDO DE LA PORTADA -->
<div class="row mb-3">

    <!-- IZQUIERDA --->
    <div class="col-xl-6">
        @include('portada.columna', ['items' => $izquierdos,'tabla' => 'portada_izquierda'])
    </div>
    
    <!-- CENTRO --->
    <div class="col-xl-3">
        @include('portada.columna', ['items' => $centrales,'tabla' => 'portada_central'])
    </div>


    <!-- DERECHA --->
    <div class="col-xl-3">
        @include('portada.columna', ['items' => $derechos,'tabla' => 'portada_derecha'])
    </div>
    

    
</div>


<!-- OTROS BANNERS -->
<div class="row mb3">
    <div class="col-xl-9">

        <div class="card">
            <div class="card-header position-relative">
                <h4 class="card-title">Banner Entre Párrafos</h4>
                
            </div>
            <div class="card-body" data-tabla="portada_slider" id="listaOrdenableBannersSobrecabecera" style="overflow:hidden;">

                @include('portada.banner',['tabla'=>'portada',
                    'banner'=>'banner_izquierda',
                    'codigo'=>$portada->banner_izquierdaCodigoFuente,
                    'imagen' => $portada->banner_izquierdaImagen,
                    'titulo' => $portada->banner_izquierdaTitulo,
                    'width'=>config('gecox_portada.banners.cabecera.ancho', '900'),
                    'height'=>config('gecox_portada.banners.cabecera.alto', '150')]
                )
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card">
        </div>
    </div>
</div>




@include('portada.modal-banner')

@include('portada.modal-noticia')



@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>

    <!-- gecox_portada -->
    <script>
            window.csrfToken = "{{ csrf_token() }}";
            window.noticiaDatosUrl = "{{ route('noticia.datos') }}";
            window.bannerDatosUrl = "{{ route('banner.datos') }}";
            window.eliminarUrl = "{{ route('portada.eliminar') }}";
            window.reordenarUrl = "{{ route('portada.reordenar') }}";
            window.publicacionBuscarUrl = "{{ route('publicacion.buscar') }}";
    </script>
    @vite('resources/gecox/js/gecox_portada.init.js')

    
@endsection