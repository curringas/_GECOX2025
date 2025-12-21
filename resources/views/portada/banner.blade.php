<div class="card overflow-hidden p-2 text-center" style="overflow:hidden;">
    <!-- Botón editar arriba a la derecha -->
    <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
        
        <button type="button" 
                data-tabla="{{ $tabla }}" 
                data-banner="{{ $banner }}" 
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal" 
                data-bs-target="#banner">
            <i class="mdi mdi-pencil"></i> Editar
        </button>

        <button type="button" 
                data-tabla="{{ $tabla }}" 
                data-eliminar="{{ $banner }}" 
                class="btn btn-sm btn-danger">
                    <i class="mdi mdi-delete"></i>
        </button>
    </div>
    @if (isset($portada) && $codigo)
        {{ str_replace(config('gecox_portada.banners.cabecera.ancho', '610'), config('gecox_portada.banners.cabecera.ancho', '728'), str_replace("105", "112", $codigo)) }}
    @elseif (isset($portada) && $imagen)
        <img src="{{ asset('storage/'.$imagen) }}" 
            alt="{{ $titulo}}"
            title="{{ $titulo}}" 
            width="{{ $width }}"
            height="{{ $height }}"
            class="img-fluid">
    @else
        <p>No hay banner asignado</p>
    @endif
</div>