@extends('layouts.master')

@section('title') @lang('translation.Dashboards') @endsection

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
                        class="btn btn-sm btn-primary position-absolute top-0 end-0 m-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-pencil"></i> Nuevo banner
                </button>
            </div>
            <div class="card-body" style="overflow:hidden;">

                @foreach($sliders as $slider)
                    <div class="mb-4 position-relative">
                        <!-- Botón editar arriba a la derecha -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                            
                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-banner="Banner"  
                                    data-orden="{{ $slider->Orden }}" 
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#banner">
                                <i class="mdi mdi-pencil"></i> Editar
                            </button>

                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-banner-eliminar="Banner" 
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
        <div class="card overflow-hidden p-2 text-center" style="overflow:hidden;">
            <!-- Botón editar arriba a la derecha -->
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                
                <button type="button" 
                        data-tabla="portada" 
                        data-banner="banner_cabecera" 
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-pencil"></i> Editar
                </button>

                <button type="button" 
                        data-tabla="portada" 
                        data-banner-eliminar="banner_cabecera" 
                        class="btn btn-sm btn-danger">
                         <i class="mdi mdi-delete"></i>
                </button>
            </div>

            @if (isset($portada) && $portada->banner_cabeceraCodigoFuente)
                {{ str_replace(config('gecox_portada.banners.cabecera.ancho', '610'), config('gecox_portada.banners.cabecera.ancho', '728'), str_replace("105", "112", $portada->banner_cabeceraCodigoFuente)) }}
            @elseif (isset($portada) && $portada->banner_cabeceraImagen)
                <img src="{{ asset('storage/'.$portada->banner_cabeceraImagen) }}" 
                    alt="{{ $portada->banner_cabeceraTitulo}}"
                    title="{{ $portada->banner_cabeceraTitulo}}" 
                    width="{{ config('gecox_portada.banners.cabecera.ancho', '728') }}"
                    height="{{ config('gecox_portada.banners.cabecera.alto', '90') }}"
                    class="img-fluid">
            @endif
        </div>
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
        <div class="card overflow-hidden position-relative h-100" style="background: url('{{ URL::asset('images/1641266162.jpg') }}') center center / cover no-repeat; min-height: 220px;">
            <!-- Botón editar arriba a la derecha -->
            <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2"
                    data-bs-toggle="modal" data-bs-target="#modalEditarFondoIzquierda">
                <i class="mdi mdi-pencil"></i> Editar
            </button>
            <div class="d-flex flex-column justify-content-end h-100 w-100" style="background: rgba(255,255,255,0.0);">
                <div class="p-3 text-primary" style="background: rgba(255,255,255,0.7); border-radius: 0 0 12px 12px;">
                    <h5 class="text-primary mb-1">Welcome Back !</h5>
                    <p class="mb-0">Skote Dashboard</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal editar fondo izquierda -->
    <div class="modal fade" id="modalEditarFondoIzquierda" tabindex="-1" aria-labelledby="modalEditarFondoIzquierdaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarFondoIzquierdaLabel">Editar fondo izquierda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí tu formulario de edición -->
                    <form>
                        <div class="mb-3">
                            <label for="fondoTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="fondoTitulo" value="Welcome Back !">
                        </div>
                        <div class="mb-3">
                            <label for="fondoTexto" class="form-label">Texto</label>
                            <textarea class="form-control" id="fondoTexto" rows="2">Skote Dashboard</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fondoImagen" class="form-label">Imagen de fondo</label>
                            <input type="file" class="form-control" id="fondoImagen">
                        </div>
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CENTRO --->
    <div class="col-xl-3">
        <div class="card overflow-hidden position-relative p-3 d-flex align-items-center" style="min-height: 140px;">
            <!-- Botón editar arriba a la derecha -->
            <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2"
                    data-bs-toggle="modal" data-bs-target="#modalEditarNoticiaCentral">
                <i class="mdi mdi-pencil"></i> Editar
            </button>
            <div class=" w-100 align-items-center">
                <!-- Imagen de la noticia -->
                <img src="{{ URL::asset('images/1641266162.jpg') }}"
                     alt="Noticia" class="img-fluid rounded  me-3" style="width: 100px; height: 100px; object-fit: cover;">
                <div class="mt-3">
                    <h5 class="mb-2">{{ $noticiaCentral->titulo ?? 'Título de la noticia' }}</h5>
                    <p>01/12/2025 | Redacción</p>
                    <p class="mb-0 text-muted">Subdelegacion acogerá un acto institucional en el 47º Aniversario de la Constitución</p>
                </div>
            </div>
        </div>
    </div>
    

    <!-- DERECHA --->
    <div class="col-xl-3">
        <div class="card overflow-hidden position-relative p-3 d-flex align-items-center" style="min-height: 180px;">
            <!-- Botón editar arriba a la derecha -->
            <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-0 m-2"
                    data-bs-toggle="modal" data-table="derecha" data-bs-target="#banner">
                <i class="mdi mdi-pencil"></i> Banner
            </button>
            <!-- Botón editar arriba a la derecha -->
            <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-15 m-2"
                    data-bs-toggle="modal" data-table="derecha" data-bs-target="#banner">
                <i class="mdi mdi-pencil"></i> Auto
            </button>
            <!-- Botón editar arriba a la derecha -->
            <button type="button" class="btn btn-sm btn-primary position-absolute top-0 end-10 m-2"
                    data-bs-toggle="modal" data-table="derecha" data-bs-target="#banner">
                <i class="mdi mdi-pencil"></i> Noticia
            </button>
            <!-- Video de YouTube embebido -->
            <div class="w-100 d-flex justify-content-center align-items-center" style="height:140px;">
                <iframe width="100%" height="140"
                    src="https://www.youtube.com/embed/{{ $videoDerechaId ?? 'dQw4w9WgXcQ' }}"
                    title="YouTube video" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
        </div>
    </div>

    
</div>



<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<!-- Modal banner -->
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

    <div class="modal fade" id="banner" tabindex="-1" aria-labelledby="bannerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerLabel">Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí tu formulario de edición -->
                    <form method="POST" action="{{ route('banner.guardar') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="bannerBanner" name="bannerBanner" value="">
                        <input type="hidden" id="bannerTabla" name="bannerTabla" value="">
                        <input type="hidden" id="bannerOrden" name="bannerOrden" value="">
                        <div class="mb-3">
                            <label for="bannerTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="bannerTitulo" id="bannerTitulo" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="bannerImagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" name="bannerImagen" id="bannerImagen">
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="bannerUrl" class="form-label">Url de destino</label>
                                    <input type="text" class="form-control" name="bannerUrl" id="bannerUrl">
                                </div>
                                <div class="col-md-4">
                                    <label for="bannerDestino" class="form-label">Abrir en</label>
                                    <select class="form-select" id="bannerDestino" name="bannerDestino">
                                        <option value="_self">Misma ventana</option>
                                        <option value="_blank">Nueva ventana</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bannerCodigo" class="form-label">Código fuente <span class="text-warning">(Este código tendra preferencia sobre la imagen subida)</span></label>
                            <textarea class="form-control" name="bannerCodigo" id="bannerCodigo" rows="4"></textarea>
                        </div>
                        <!-- Añade más campos según lo que quieras editar -->
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <!-- gecox_portada -->
    <script>
            window.csrfToken = "{{ csrf_token() }}";
            window.bannerDatosUrl = "{{ route('banner.datos') }}";
            window.bannerEliminarUrl = "{{ route('banner.eliminar') }}";
    </script>
    <script src="{{ URL::asset('build/js/pages/gecox_portada.init.js') }}"></script>

    
@endsection