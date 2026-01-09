@extends('layouts.master')

@section('title')
    @lang('titulos.Banners')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('build/libs/daterangepicker/daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('build/libs/datatable-filtros/css/filtros.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css" />

    <style>
        /* handle visual y cursor de arrastre */
        .yajra-datatable td.col-orden .handle {
            display:inline-block;
            padding:2px 6px;
            cursor: grab;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            font-weight: 600;
        }
        .yajra-datatable td.col-orden .handle:active { cursor: grabbing; }

        /* columna Orden estrecha */
        .yajra-datatable th.col-orden,
        .yajra-datatable td.col-orden {
            white-space: nowrap;
            text-align: center;
            width: 70px;
            max-width: 70px;
        }

        /* Alineado vertical en todas las celdas de la tabla */
        .yajra-datatable th,
        .yajra-datatable td {
            vertical-align: middle !important;
        }

        /* Asegurar que imágenes y contenidos inline estén centrados verticalmente */
        .yajra-datatable td img,
        .yajra-datatable td .handle {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Banners')
        @endslot
    @endcomponent

    <div class="datatable-filtros">
        <input type="search" id="search-box" class="form-control" placeholder="Buscar..." style="max-width:180px;">
        <select id="tipo-filter" class="form-select" style="max-width:180px;">
            <option value="">Tipo</option>
            <option value="0">{{ config('gecox_banners.tipos.0') }}</option>
            <option value="1">{{ config('gecox_banners.tipos.1') }}</option>
            <option value="2">{{ config('gecox_banners.tipos.2') }}</option>
        </select>
        <select id="categoria-filter" class="form-select" style="max-width:180px;">
            <option value="">Categoría</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->Identificador }}">
                        {{ $cat->Etiqueta }}
                    </option>
                    @if($cat->hijos && $cat->hijos->count())
                        @foreach($cat->hijos as $sub) 
                            <option value="{{ $sub->Identificador }}">
                                --- {{ $sub->Etiqueta }}
                            </option>

                            @if($sub->hijos && $sub->hijos->count())
                                @foreach($sub->hijos as $nieto) 
                                    <option value="{{ $nieto->Identificador }}">
                                        ------ {{ $nieto->Etiqueta }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </select>
        <a href="{{ route('banners.create') }}" class="btn btn-primary end-0 ms-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i> Crear banner
        </a>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th>Orden</th> {{-- nueva columna visible sólo si filtro categoria --}}                
                <th>Banner</th>
                <th>Título</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Target</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- RowReorder JS (CDN si no lo tienes local) -->
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>

    <script src="{{ URL::asset('build/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/daterangepicker/daterangepicker.min.js') }}"></script>

    <script type="text/javascript">
        $(function() {

            var table = $('.yajra-datatable').DataTable({
                autoWidth: true,
                order: [[1, 'desc']],
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25,
                pageLengthMenu: [10, 25, 50, 100],
                rowReorder: {
                    // el handle será el span.handle dentro de la celda Orden
                    selector: 'td.col-orden .handle',
                    snapX: true
                },
                ajax: {
                    url: "{{ route('banners.index') }}",
                    data: function(d) {
                        d.search = $('#search-box').val();
                        d.tipo = $('#tipo-filter').val();
                        d.categoria = $('#categoria-filter').val();
                    }
                },
                columns: [
                    {
                        data: 'Orden',
                        name: 'Orden',
                        orderable: true,
                        searchable: false,
                        defaultContent: '',
                        className: 'col-orden',
                        render: function(data, type, row) {
                            var value = (data !== null && data !== undefined && data !== '') ? data : '';
                            // handle visible (≡) + número
                            return '<span class="handle" style="font-size:large">&#9776;</span> ';
                        }
                    },
                    { data: 'Banner', name: 'Banner' },
                    { data: 'Titulo', name: 'Titulo' },
                    { data: 'Fecha', name: 'Fecha' },
                    { data: 'Tipo', name: 'Tipo' },
                    { data: 'Target', name: 'Target' },
                    { 
                        data: 'action', 
                        name: 'action',  
                        orderable: false, 
                        searchable: false
                    }
                ],
                columnDefs: [
                    { targets: 0, visible: ($('#categoria-filter').val() && $('#tipo-filter').val() ? true : false), className: 'col-orden' },
                    { 
                        // 2. Centrar el contenido de las acciones
                        "className": "text-center", 
                        "targets": 6 
                    },
                ],
                initComplete: function() {
                    adjustAndRecalc();
                }
            });

            // comprobar disponibilidad rowReorder
            console.log('RowReorder enabled in table:', !!table.rowReorder);

            function adjustAndRecalc() {
                try { table.columns.adjust(); } catch(e) {}
                try {
                    if (table && typeof table.responsive === 'function') {
                        var respApi = table.responsive();
                        if (respApi && typeof respApi.recalc === 'function') respApi.recalc();
                    } else if (table && table.responsive && typeof table.responsive.recalc === 'function') {
                        table.responsive.recalc();
                    }
                } catch (e) {}
            }

            function updateReorderEnabled() {
                var cat = $('#categoria-filter').val();
                var tip = $('#tipo-filter').val();
                console.log('updateReorderEnabled: categoria=', cat, ' tipo=', tip);
                if (cat && tip) {
                    try { table.rowReorder.enable(); } catch(e) { console.warn('rowReorder.enable error', e); }
                    table.column(0).visible(true);
                } else {
                    try { table.rowReorder.disable(); } catch(e) { console.warn('rowReorder.disable error', e); }
                    table.column(0).visible(false);
                }
                // recargar para obtener/actualizar la columna Orden
                table.ajax.reload(null, false);
            }

            // evento: cuando se complete un reorden (rowReorder)
            table.on('row-reorder', function(e, diff, edit) {
                console.log('row-reorder event diff:', diff, 'edit:', edit);
                var cat = $('#categoria-filter').val();
                var tip = $('#tipo-filter').val();
                if (!cat || !tip) return;
                if (!diff || !diff.length) return;

                // Obtener array actual (orden aplicado antes del reorden en algunos navegadores)
                var rows = table.rows({ order: 'applied' }).data().toArray();

                // Aplicar los movimientos que vienen en diff para componer el nuevo orden
                // Cada elemento diff tiene: node (tr DOM), oldPosition, newPosition
                diff.forEach(function(move) {
                    var movedData = table.row(move.node).data();
                    if (!movedData) return;

                    // buscar índice actual en el array
                    var curIndex = rows.findIndex(function(r) { return r.Identificador == movedData.Identificador; });
                    if (curIndex === -1) return;

                    // extraer y volver a insertar en la nueva posición
                    rows.splice(curIndex, 1);
                    rows.splice(move.newPosition, 0, movedData);
                });

                // Construir payload con la nueva secuencia
                // Usar índice 0-based para que en BD se guarde desde 0
                var payload = rows.map(function(r, idx) {
                    return { Banner: r.Identificador, Orden: idx };
                });

                $.ajax({
                    url: "{{ route('banners.reorder') }}",
                    method: 'POST',
                    data: {
                        categoria: cat,
                        posicion: parseInt(tip, 10),
                        orden: payload,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.ok) table.ajax.reload(null, false);
                        else alert('Error guardando orden: ' + (res.error || ''));
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Error al guardar orden');
                    }
                });
            });

            // bindings
            $('#categoria-filter').on('change', function() {
                if ($('#tipo-filter').val()) {
                    updateReorderEnabled();
                    applyCategoriaBehavior && applyCategoriaBehavior();
                }else {
                    table.draw();
                }
            });
            $('#tipo-filter').on('change', function() {
                if ($('#categoria-filter').val()) {
                    updateReorderEnabled();
                    applyCategoriaBehavior && applyCategoriaBehavior();
                }else {
                    table.draw();
                }
            });
            $('#search-box').on('keyup', function(){ table.draw(); });
            //$('#tipo-filter').on('change', function(){ table.draw(); });

            // init
            updateReorderEnabled();

            table.on('xhr.dt', function(e, settings, json, xhr) { adjustAndRecalc(); });
            table.on('draw.dt', function() { adjustAndRecalc(); });

            var resizeTimer;
            $(window).on('resize', function() { clearTimeout(resizeTimer); resizeTimer = setTimeout(adjustAndRecalc, 200); });
        });
    </script>
@endsection