<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Editar_Permiso'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php if(!empty($role)): ?> 
                <?php echo app('translator')->get('titulos.Editar_Permiso'); ?>                
            <?php else: ?>
                <?php echo app('translator')->get('titulos.Crear_Permiso'); ?>
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <form method="POST" action="<?php echo e(route('permissions.store')); ?>"  role="form" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <?php echo $__env->make('permissions.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PROYECTOS\_SKOTE-LARAVEL-template\Starterkit\resources\views/permissions/create.blade.php ENDPATH**/ ?>