<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Publicaciones'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!--datatable css-->
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" />
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/daterangepicker/daterangepicker.css')); ?>" />
        
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/datatable-filtros/css/filtros.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('titulos.Publicaciones'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="datatable-filtros">
        <input type="search" id="search-box" class="form-control" placeholder="Buscar..." style="max-width:250px;">
        <input type="text" id="date-filter" class="form-control" placeholder="Selecciona una fecha" autocomplete="off"
            style="max-width:180px;">
        
        <select id="user-filter" class="form-select" style="max-width:180px;">
            <option value="">Creado por</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($user->email); ?>"><?php echo e($user->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
            <input class="form-check-input" type="checkbox" id="active-switch" checked>
            <label class="form-check-label ms-2 mb-0" for="active-switch"><?php echo app('translator')->get('Activo'); ?></label>
        </div>
        <!-- Botón Exportar Excel -->
        
        <a href="<?php echo e(route('publicacion.create')); ?>" class="btn btn-primary end-0 ms-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i>
            <?php echo app('translator')->get('titulos.Crear_Publicacion'); ?>
        </a>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th><?php echo app('translator')->get('Fecha'); ?></th>
                <th><?php echo app('translator')->get('Titulo'); ?></th>
                <th><?php echo app('translator')->get('Categorias'); ?></th>
                <th><?php echo app('translator')->get('Autor'); ?></th>
                <th class="text-end"><?php echo app('translator')->get('Visitas'); ?></th>
                <th><?php echo app('translator')->get('Activa'); ?></th>
                <th><?php echo app('translator')->get('Acciones'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/moment/min/moment.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/daterangepicker/daterangepicker.min.js')); ?>"></script>
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
                order: [[7, 'desc']],
                processing: true,
                serverSide: true,
                responsive: true,  
                autoWidth: false,
                ajax: {
                    url: "<?php echo e(route('publicaciones.index')); ?>",
                    data: function(d) {
                        d.search = $('#search-box').val();
                        d.user = $('#user-filter').val();
                        d.active = $('#active-switch').prop('checked') ? 1 : 0;
                        d.date = $('#date-filter').val();
                    }
                },
                dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center"l i p><"clear">',
                language: {
                    url: '<?php echo e(asset('build/json/datatable_es-ES.json')); ?>'
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
                        data: 'Visitas',
                        name: 'Visitas',
                        className: 'text-end'
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


            $('#search-box').on('input', function() {
                table.draw();
            });

            $('#user-filter').on('change', function() {
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
                role: $('#user-filter').val(),
                active: $('#active-switch').prop('checked') ? 1 : 0,
                date: $('#date-filter').val()
            });
            // Redirige a la ruta de exportación con los filtros
            window.location.href = "<?php echo e(route('users.export')); ?>?" + params.toString();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Developer/_GECOX2025/resources/views/publicaciones/index.blade.php ENDPATH**/ ?>