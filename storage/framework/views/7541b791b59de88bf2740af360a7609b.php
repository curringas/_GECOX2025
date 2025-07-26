<div class="row">
    <div class="col-md-6">       
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-8">   
                        <div class="mb-3">
                            <label for="name" class="form-label"><?php echo app('translator')->get("translation.Nombre"); ?>*</label>
                            <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $role?->name)); ?>" id="name" placeholder="<?php echo app('translator')->get("translation.Nombre"); ?>" required>
                            <?php echo $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

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
                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox" id="permiso<?php echo e($permission->id); ?>" name="permissions[]" value="<?php echo e($permission->name); ?>" <?php echo e($role && $role->hasPermissionTo($permission->name) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="SwitchCheckSizemd"><?php echo e($permission->name); ?></label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                           
                        </div>    
                    </div>
                
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="text-end mb-4">
            <a href="<?php echo e(route('permissions.index')); ?>"class="btn btn-secondary"><?php echo app('translator')->get("botones.Volver"); ?></a>
            <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("botones.Guardar"); ?></button>
        </div>
    </div>
    <div class="col-lg-6">
        <?php if(!empty($role->id)): ?> 
        <div class="text-end mb-4">            
            <button type="button" class="btn btn-danger" onclick="if(confirm('<?php echo e(__('messages.estas_seguro')); ?>')) { document.getElementById('delete-form').submit(); };"><?php echo app('translator')->get('botones.Eliminar'); ?></button>
        </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH E:\xampp8.3\htdocs\started-kit-te20\resources\views/roles/form.blade.php ENDPATH**/ ?>