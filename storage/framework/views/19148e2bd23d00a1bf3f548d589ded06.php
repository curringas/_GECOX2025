<?php $__env->startSection('title'); ?> Editar Banner <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?> Backend <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Editar Banner <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form method="POST" action="<?php echo e(route('banners.store')); ?>" role="form" enctype="multipart/form-data">
        <?php echo e(method_field('PATCH')); ?>

        <?php echo csrf_field(); ?>

        <?php echo $__env->make('banners.form', ['banner' => $banner ?? null, 'categorias' => $categorias ?? \App\Models\Categoria::whereNull('Padre')->get()], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </form>

    <?php if(isset($banner)): ?>

    <form action="<?php echo e(route('banner.destroy', ['id' => $banner->Identificador])); ?>" method="POST" style="display:none" id="delete-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button  class="btn btn-danger" type="submit" id="delete-button">Eliminar banner</button>

    </form>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/gecox_banners.init.js')); ?>"></script>
    <script>
        $(function(){ $('.select2').select2({ width: '100%' }); });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Developer/_GECOX2025/resources/views/banners/edit.blade.php ENDPATH**/ ?>