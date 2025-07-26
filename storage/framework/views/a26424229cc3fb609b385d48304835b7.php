<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Permisos'); ?>
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
            <?php echo app('translator')->get('titulos.Permisos'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="float-left">
                <span class="card-title">&nbsp;</span>
            </div>      
            <div class="float-right">
                <a class="btn btn-primary btn-sm" href="<?php echo e(route('permissions.index')); ?>"> <?php echo e(__('botones.Volver')); ?></a>
            </div>
        </div>
        </div>
        <div class="col-md-6">       
            <div class="card">
                <div class="card-body"> 
                    <h4 class="card-title mb-4"><?php echo app('translator')->get("translation.Nombre"); ?></h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">                                
                                <?php echo e($permission?->name); ?>

                            </div>    
                        </div>                
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">      
            <div class="card">
                <div class="card-body">   
                    <h4 class="card-title mb-4"><?php echo app('translator')->get('titulos.Roles'); ?> que lo contiene</h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">
                                <ul>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($role->hasPermissionTo($permission->name)): ?>                                        
                                        <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                            <li><?php echo e($role->name); ?></li>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                           
                                </ul>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PROYECTOS\_SKOTE-LARAVEL-template\Starterkit\resources\views/permissions/show.blade.php ENDPATH**/ ?>