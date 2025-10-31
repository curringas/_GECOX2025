<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Categorias'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/datatable-filtros/css/filtros.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('titulos.Categorias'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="datatable-filtros">
        
        <a href="<?php echo e(route('categorias.index')); ?>" class="btn btn-primary end-0">
            <i class="bx bx-smile font-size-16 align-middle me-2"></i>
            <?php echo app('translator')->get('titulos.Crear_Categoria'); ?>
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Arbol de Categorías</h4>
                    
                    <div class="dd" id="categoryTree">
                        <ol class="dd-list">
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('categorias.partials.categoria-item', ['categoria' => $categoria], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                
                <div class="card-body">
                    <h4 class="card-title">
                        <?php if(isset($amodificar)): ?>
                            <?php echo e($amodificar->Etiqueta); ?>

                        <?php else: ?>
                            Nueva categoría
                        <?php endif; ?>
                    </h4>

                    <?php echo $__env->make('categorias.partials.categoria-form', [
                        'amodificar' => $amodificar ?? null,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    


    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

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
                    url: '<?php echo e(route('categorias.reorder')); ?>',
                    method: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/categorias/index.blade.php ENDPATH**/ ?>