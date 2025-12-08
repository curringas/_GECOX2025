<div class="card">
            <div class="card-header position-relative">
                <h4 class="card-title">&nbsp;</h4>
                <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                <!-- Noticia automática 
                <button type="button"
                        data-tabla="{{ $tabla }}"
                        data-auto="1"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#auto">
                    <i class="mdi mdi-plus"></i> Auto
                </button>-->
                <!-- Noticia -->
                <button type="button" 
                        data-tabla="{{ $tabla }}"
                        data-noticia="1"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#noticia">
                    <i class="mdi mdi-plus"></i> Noticia
                </button>
                <!-- Banner -->
                <button type="button" 
                        data-tabla="{{ $tabla }}" 
                        data-banner="Banner"  
                        data-id="nuevo"
                        data-orden="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-plus"></i>Banner
                </button>
                </div>
            </div>
            <div class="card-body lista-ordenable p-2" data-tabla="{{ $tabla }}" id="lista_{{ $tabla }}" style="overflow:hidden;">

                @foreach($items as $item)
                    @include('portada.item', ['clase' => $item,'tabla' => $tabla])
                @endforeach
            </div>
        </div>