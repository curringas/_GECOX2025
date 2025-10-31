<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Usuarios'); ?>
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
            <?php echo app('translator')->get('titulos.Usuarios'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php if($message = Session::get('success')): ?>
        <div class="alert alert-success">
            <p><?php echo e($message); ?></p>
        </div>
    <?php endif; ?>

    <div class="datatable-filtros">
        <input type="text" id="date-filter" class="form-control" placeholder="Selecciona una fecha" autocomplete="off"
            style="max-width:180px;">
        <input type="search" id="search-box" class="form-control" placeholder="Buscar..." style="max-width:180px;">
        <select id="role-filter" class="form-select" style="max-width:180px;">
    <option value="">Rol</option>
    <?php $__currentLoopData = \Spatie\Permission\Models\Role::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
        <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
    <input class="form-check-input" type="checkbox" id="active-switch" checked>
    <label class="form-check-label ms-2 mb-0" for="active-switch"><?php echo app('translator')->get('Activo'); ?></label>
</div>
        <!-- Botón Exportar Excel -->
        <a href="#" onclick="exportExcel(); return false;" class="btn btn-success end-0">
            <i class="bx bx-download font-size-16 align-middle me-2"></i>
            Exportar Excel
        </a>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary end-0 ms-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i>
            <?php echo app('translator')->get('titulos.Crear_Usuario'); ?>
        </a>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th>No</th>
                <th><?php echo app('translator')->get('Name'); ?></th>
                <th><?php echo app('translator')->get('Email'); ?></th>
                <th><?php echo app('translator')->get('Dob'); ?></th>
                <th><?php echo app('translator')->get('Avatar'); ?></th>
                <th><?php echo app('translator')->get('Activo'); ?></th>
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
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('users.index')); ?>",
                    data: function(d) {
                        d.search = $('#search-box').val();
                        d.role = $('#role-filter').val();
                        d.active = $('#active-switch').prop('checked') ? 1 : 0;
                        d.date = $('#date-filter').val();
                    }
                },
                dom: '<"top">rt<"bottom d-flex justify-content-between align-items-center"l i p><"clear">',
                language: {
                    url: '<?php echo e(asset('build/json/datatable_es-ES.json')); ?>'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'avatar',
                        name: 'avatar'
                    },
                    {
                        data: 'activo',
                        name: 'activo'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
            window.location.href = "<?php echo e(route('users.export')); ?>?" + params.toString();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/users/index.blade.php ENDPATH**/ ?>