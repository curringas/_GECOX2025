<li class="dd-item" data-id="{{ $categoria->Identificador }}">
    <div class="dd-content d-flex justify-content-between align-items-center">
        <div class="dd-handle flex-grow-1" style="{{ $categoria->Menu!=0 ? 'font-weight:normal' : '' }}">
            {{ $categoria->Etiqueta ?? 'Sin título' }} 
            {{ $categoria->Menu!=0 ? ' [Oculta]' : '' }}
        </div>
        <a href="{{ route('categorias.edit', $categoria->Identificador) }}" 
           class="btn btn-sm btn-outline-primary ms-2">
            Modificar
        </a>
    </div>

    @if ($categoria->hijos && $categoria->hijos->count())
        <ol class="dd-list">
            @foreach ($categoria->hijos as $hijo)
                @include('categorias.partials.categoria-item', ['categoria' => $hijo])
            @endforeach
        </ol>
    @endif
</li>
