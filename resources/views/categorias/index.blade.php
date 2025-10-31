@extends('layouts.master')

@section('title')
    @lang('titulos.Categorias')
@endsection

@section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/datatable-filtros/css/filtros.css') }}" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Categorias')
        @endslot
    @endcomponent

    <div class="datatable-filtros">
        
        <a href="{{ route('categorias.index') }}" class="btn btn-primary end-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i>
            @lang('titulos.Crear_Categoria')
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Arbol de Categorías</h4>
                    {{-- Árbol de categorías --}}
                    <div class="dd" id="categoryTree">
                        <ol class="dd-list">
                            @foreach ($categorias as $categoria)
                                @include('categorias.partials.categoria-item', ['categoria' => $categoria])
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                {{-- @dd($amodificar ?? 'no hay nada'); --}}
                <div class="card-body">
                    <h4 class="card-title">
                        @if (isset($amodificar))
                            {{ $amodificar->Etiqueta }}
                        @else
                            Nueva categoría
                        @endif
                    </h4>

                    @include('categorias.partials.categoria-form', [
                        'amodificar' => $amodificar ?? null,
                    ])
                </div>
            </div>
        </div>
    </div>
    


    
@endsection

@section('script')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.css">
    <script src="https://cdn.jsdelivr.net/npm/nestable2@1.6.0/jquery.nestable.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            const $tree = $('#categoryTree').nestable({
                maxDepth: 5
            });

            // Escucha cuando el árbol cambia
            $tree.on('change', function () {
                const data = $tree.nestable('serialize');
                guardarOrden(data);
            });

            function guardarOrden(data) {
                $('#status').text('Guardando orden...');

                $.ajax({
                    url: '{{ route('categorias.reorder') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        tree: data
                    },
                    success: function (res) {
                        $('#status').text('✅ Orden actualizado correctamente');
                    },
                    error: function () {
                        $('#status').text('❌ Error al guardar el orden');
                    }
                });
            }

            
        });

    </script>
@endsection