<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Editar_Publicacion'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('titulos.Editar_Publicacion'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form method="POST" action="<?php echo e(route('publicacion.store')); ?>"  role="form" enctype="multipart/form-data">
        <?php echo e(method_field('PATCH')); ?>

        <?php echo csrf_field(); ?>

        <?php echo $__env->make('publicaciones.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </form>
    <?php if(isset($publicacion)): ?>
    <form action="<?php echo e(route('publicacion.destroy', ['id' => $publicacion->Identificador])); ?>" method="POST" style="display:none;" id="delete-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>        
    </form> 
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- bootstrap datepicker -->
    <script src="<?php echo e(URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')); ?>"></script>
    <!-- Idioma español del deatepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.es.min.js"></script>
    
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/xpt_categorias.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/pages/xpt_seo.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/palabrea_publicaciones.init.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script-bottom'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/publicaciones/edit.blade.php ENDPATH**/ ?>