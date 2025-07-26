<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Usuarios'); ?>
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
            <?php echo app('translator')->get('titulos.Usuarios'); ?>
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
                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                    <i class="bx bx-smile font-size-16 align-middle me-2"></i>
                    <?php echo app('translator')->get("titulos.Crear_Usuario"); ?></a>
            </div>
        </div>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead class="thead">
            <tr>
                <th>No</th>                                        
                <th >Name</th>
                <th >Email</th>
                <th >Dob</th>
                <th >Avatar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(++$i); ?></td>
                    
                    <td ><?php echo e($user->name); ?></td>
                    <td ><?php echo e($user->email); ?></td>
                    <td ><?php echo e(Carbon\Carbon::parse($user->dob)->format("d/m/Y")); ?></td>
                    <td ><?php echo e($user->avatar); ?></td>

                    <td>
                        <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST">
                            <a class="btn btn-sm btn-primary " href="<?php echo e(route('users.show', $user->id)); ?>"><i class="fa fa-fw fa-eye"></i> <?php echo e(__('Show')); ?></a>
                            <a class="btn btn-sm btn-success" href="<?php echo e(route('users.edit', $user->id)); ?>"><i class="fa fa-fw fa-edit"></i> <?php echo e(__('Edit')); ?></a>
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> <?php echo e(__('Delete')); ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo $users->withQueryString()->links(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PROYECTOS\_SKOTE-LARAVEL-template\Starterkit\resources\views/users/index.blade.php ENDPATH**/ ?>