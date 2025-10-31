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
    <?php if($message = Session::get('success')): ?>
        <div class="alert alert-success">
            <p><?php echo e($message); ?></p>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <div class="text-end mb-4">
                <a href="<?php echo e(route('permissions.create')); ?>" class="btn btn-primary">
                    <i class="bx bx-smile font-size-16 align-middle me-2"></i>
                    <?php echo app('translator')->get("titulos.Crear_Permiso"); ?></a>
            </div>
        </div>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead class="thead">
            <tr>
                <th>No</th>                                        
                <th><?php echo app('translator')->get("translation.Nombre"); ?></th>
                <th>Roles</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(++$i); ?></td>
                    
                    <td ><?php echo e($permission->name); ?></td>
                    <td >
                        <?php $__currentLoopData = $permission->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($role->name); ?>,
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>

                    <td>
                        <form action="<?php echo e(route('permissions.destroy', $permission->id)); ?>" method="POST">
                            <a class="btn btn-sm btn-primary " href="<?php echo e(route('permissions.show', $permission->id)); ?>"><i class="fa fa-fw fa-eye"></i> <?php echo e(__('botones.Ver')); ?></a>
                            <a class="btn btn-sm btn-success" href="<?php echo e(route('permissions.edit', $permission->id)); ?>"><i class="fa fa-fw fa-edit"></i> <?php echo e(__('botones.Editar')); ?></a>
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('<?php echo e(__('messages.estas_seguro')); ?>') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> <?php echo e(__('botones.Eliminar')); ?></button>
                        </form>
                    </td>
                    
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo $permissions->withQueryString()->links(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/permissions/index.blade.php ENDPATH**/ ?>