

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Usuario'); ?>
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
            <?php echo app('translator')->get('titulos.Usuario'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-md-6">       
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4"><?php echo app('translator')->get('translation.User_DatosPersonales'); ?></h4>     
                    <div class="row">
                    <div class="col-md-8">   
                        <div class="mb-3">
                            <b>Nombre:</b> <?php echo e($user?->name); ?>                            
                        </div>
                        <div class="mb-3">
                            <b>Email:</b>  <?php echo e($user?->email); ?>                            
                        </div>
                        
                        <div class="mb-3">
                            <b>Role:</b> <?php echo e($user->roles[0]->name); ?>

                        </div>  
                        <div class="mb-3">                            
                            <b>Fecha Nacimiento:</b> <?php echo e(\Carbon\Carbon::parse($user->dob)->format("d/m/Y")); ?>

                        </div>      
                    </div>
                    <div class="col-md-4">
                    
                    
                        <div class="mb-3">
                            <label class="form-label">Avatar</label>
                            
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute bottom-0 end-0">
                                        <label for="project-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-light border rounded-circle text-muted cursor-pointer shadow font-size-16">
                                                    <i class="bx bxs-image-alt"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="avatar-lg">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <img src="<?php echo e(URL::asset('storage/avatares/'.$user->avatar)); ?>" id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">      
            <div class="card">
                <div class="card-body">   
                    <h4 class="card-title mb-4"><?php echo app('translator')->get('translation.User_Permisos'); ?></h4>
                    <div class="row">
                        <div class="col-md-12">   
                            <div class="mb-3">
                                <ul>
                                <?php $__currentLoopData = $user->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($permission->name); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                           
                                </ul>
                            </div>    
                        </div>                
                    </div>    
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6">
            <div class="text-end mb-4">
                <a href="<?php echo e(route('users.index')); ?>"class="btn btn-secondary"><?php echo app('translator')->get("botones.Volver"); ?></a>
                <a href="<?php echo e(route('users.edit',$user)); ?>"class="btn btn-primary"><?php echo app('translator')->get("botones.Editar"); ?></a>
            </div>
        </div>
        <div class="col-lg-6">
            <?php if(!empty($user->id)): ?> 
                <div class="text-end mb-4">        
                    

                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST" style="display:none;" id="delete-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>        
                    </form>     
                    <button type="button" class="btn btn-danger" onclick="if(confirm('<?php echo e(__('messages.estas_seguro')); ?>')) { document.getElementById('delete-form').submit(); };"><?php echo app('translator')->get('botones.Eliminar'); ?></button>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\xampp8.3\htdocs\started-kit-te20\resources\views/users/show.blade.php ENDPATH**/ ?>