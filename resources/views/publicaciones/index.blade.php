@extends('layouts.master')

@section('title')
    @lang('titulos.Publicaciones')
@endsection

@section('css')
    <!--datatable css-->
        <link rel="stylesheet" href="{{ URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/daterangepicker/daterangepicker.css') }}" />
        
        <link rel="stylesheet" href="{{ URL::asset('build/libs/datatable-filtros/css/filtros.css') }}" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Publicaciones')
        @endslot
    @endcomponent

    <div class="datatable-filtros">
        <input type="text" id="date-filter" class="form-control" placeholder="Selecciona una fecha" autocomplete="off"
            style="max-width:180px;">
        <input type="search" id="search-box" class="form-control" placeholder="Buscar..." style="max-width:180px;">
        <select id="role-filter" class="form-select" style="max-width:180px;">
    <option value="">Rol</option>
    @foreach (\Spatie\Permission\Models\Role::all() as $role)
        <option value="{{ $role->name }}">{{ $role->name }}</option>
    @endforeach
</select>
        <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
    <input class="form-check-input" type="checkbox" id="active-switch" checked>
    <label class="form-check-label ms-2 mb-0" for="active-switch">@lang('Activo')</label>
</div>
        <!-- Botón Exportar Excel -->
        <a href="#" onclick="exportExcel(); return false;" class="btn btn-success end-0">
            <i class="bx bx-download font-size-16 align-middle me-2"></i>
            Exportar Excel
        </a>
        <a href="{{ route('publicacion.create') }}" class="btn btn-primary end-0 ms-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i>
            @lang('titulos.Crear_Publicacion')
        </a>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th>@lang('Fecha')</th>
                <th>@lang('Titulo')</th>
                <th>@lang('Categorias')</th>
                <th>@lang('Autor')</th>
                <th>@lang('Activa')</th>
                <th>@lang('Acciones')</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/daterangepicker/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            

            // Cambia a selección de solo 1 fecha
            $('#date-filter').daterangepicker({
                singleDatePicker: false,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#date-filter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')
                );
                table.draw();
            });

            $('#date-filter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.draw();
            });

            var table = $('.yajra-datatable').DataTable({
                order: [[6, 'desc']],
                processing: true,
                serverSide: true,
                responsive: true,  
                autoWidth: false,
                ajax: {
                    url: "{{ route('publicaciones.index') }}",
                    data: function(d) {
                        d.search = $('#search-box').val();
                        d.role = $('#role-filter').val();
                        d.active = $('#active-switch').prop('checked') ? 1 : 0;
                        d.date = $('#date-filter').val();
                    }
                },
                dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center"l i p><"clear">',
                language: {
                    url: '{{ asset('build/json/datatable_es-ES.json') }}'
                },
                columns: [
                    {
                        data: 'Fecha',
                        name: 'Fecha'
                    },
                    {
                        data: 'Titulo',
                        name: 'Titulo'
                    },
                    {
                        data: 'Categorias',
                        name: 'Categorias'
                    },
                    {
                        data: 'Autor',
                        name: 'Autor'
                    },
                    {
                        data: 'Activa',
                        name: 'Activa'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
                    },
                ]
            });


            $('#search-box').on('keyup', function() {
                table.draw();
            });

            $('#role-filter').on('change', function() {
                table.draw();
            });

            $('#active-switch').on('change', function() {
                table.draw();
            });
        });

        function exportExcel() {
            // Obtén los valores de los filtros
            let params = new URLSearchParams({
                search: $('#search-box').val(),
                role: $('#role-filter').val(),
                active: $('#active-switch').prop('checked') ? 1 : 0,
                date: $('#date-filter').val()
            });
            // Redirige a la ruta de exportación con los filtros
            window.location.href = "{{ route('users.export') }}?" + params.toString();
        }
    </script>
@endsection