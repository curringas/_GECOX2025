{{--  CADA ITEM DE CADA NOTICIA O BANNER --}}
<div class="mb-4 position-relative shadow-sm p-1" data-id="{{ $clase->Identificador }}">
    <!-- Botón editar arriba a la derecha -->
    <div class="position-absolute top-0 end-0 mt-0 d-flex gap-1">
        
        <button type="button" 
                data-tabla={{ $tabla }} 
                data-banner="Banner"  
                data-id="{{ $clase->Identificador }}" 
                data-orden="{{ $clase->Orden }}" 
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal" 
                data-bs-target="#banner">
            <i class="mdi mdi-pencil"></i> Editar
        </button>

        <button type="button" 
                data-tabla={{ $tabla }} 
                data-banner-eliminar="Banner" 
                data-id="{{ $clase->Identificador }}" 
                data-orden="{{ $clase->Orden }}" 
                class="btn btn-sm btn-danger">
                <i class="mdi mdi-delete"></i>
        </button>
    </div>
    @if ($clase->Publicacion)
        {{-- Si es la columna izquierda y el primer elemento se muestra diferente --}}
        @if ($tabla == 'portada_izquierda' && $clase->Orden == 1)
            <div class="card overflow-hidden position-relative" style="background: url('{{ URL::asset('images/1641266162.jpg') }}') center center / cover no-repeat; min-height: 280px;">
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
        @else
            <div class=" w-100 align-items-center">
                <!-- Imagen de la noticia -->
                <img src="{{ URL::asset('storage/'.$clase?->publicacion?->imagenes->first()->Imagen) }}"
                    alt="Noticia" class="img-fluid rounded  me-3" style="width: 100px; height: 100px; object-fit: cover;">
                <div class="mt-3">
                    <h5 class="mb-2">{{ $clase?->publicacion->Titulo ?? 'Título de la noticia' }}</h5>
                    <p>{{ $clase?->publicacion?->Fecha->format('d/m/Y')}} | {{ $clase?->publicacion?->Autor}}</p>
                </div>
            </div>
        @endif
    @elseif ($clase->BannerCodigoFuente)
        {{ Str::limit($clase->BannerCodigoFuente,200) }}
    @elseif ($clase->BannerUrl && !$clase->BannerImagen)
        @if (strstr($clase->BannerUrl,"youtube") || strstr($clase->BannerUrl,"youtu.be"))
            <!-- Extraer el ID del video de YouTube -->
            @php
                preg_match("/(youtu\.be\/|v=)([a-zA-Z0-9_-]{11})/", $clase->BannerUrl, $matches);
                $youtubeId = $matches[2] ?? null;
            @endphp
            @if ($youtubeId)
                <iframe width="100%" height="140"
                    src="https://www.youtube.com/embed/{{ $youtubeId }}"
                    title="YouTube video" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            @endif
        @else
            {{ $clase->BannerUrl }}
        @endif
    
    @elseif ($clase->BannerImagen)
        <img src="{{ asset('storage/'.$clase->BannerImagen) }}" 
            alt="{{ $clase->BannerTitulo}}"
            title="{{ $clase->BannerTitulo}}" 
            width="{{ config('gecox_portada.banners.{$clase}.ancho', '1080') }}"
            height="{{ config('gecox_portada.banners.{$clase}.alto', '150') }}"
            class="img-fluid">
    @endif
</div>