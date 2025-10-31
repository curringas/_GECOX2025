<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4"><?php echo app('translator')->get('translation.User_DatosPersonales'); ?></h4>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label"><?php echo app('translator')->get('translation.Nombre'); ?>*</label>
                            <input type="text" name="name"
                                class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('name', $user?->name)); ?>" id="name" placeholder="<?php echo app('translator')->get('translation.Nombre'); ?>"
                                required>
                            <?php echo $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email*</label>
                            <input type="text" name="email"
                                class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('email', $user?->email)); ?>" id="email" placeholder="Email" required>
                            <?php echo $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

                        </div>
                        <?php if(empty($user)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label"><?php echo app('translator')->get('translation.Contraseña'); ?></label>
                                        <input type="password" name="newpassword"
                                            class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="newpassword" placeholder=""
                                            value="<?php echo e(old('newpassword', $user?->password)); ?>">
                                        <?php echo $errors->first('newpassword', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label"><?php echo app('translator')->get('translation.ConfirmarContraseña'); ?></label>
                                        <input type="password" name="newpassword_confirmation"
                                            class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            id="newpassword_confirmation" placeholder=""
                                            value="<?php echo e(old('newpassword_confirmation', $user?->password)); ?>">
                                        <?php echo $errors->first(
                                            'newpassword_confirmation',
                                            '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>',
                                        ); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="role" class="form-label"><?php echo app('translator')->get('translation.Roles'); ?>*</label>
                            <select class="form-select" name="role" id="role" required>
    <option value=""><?php echo app('translator')->get('translation.Seleccione'); ?></option>
    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($role->name === 'Super-admin'): ?>
            <?php if(auth()->user() && auth()->user()->hasRole('Super-admin')): ?>
                <option value="<?php echo e($role->name); ?>"
                    <?php echo e(!empty($user) && $user->hasRole($role->name) ? 'selected' : ''); ?>>
                    <?php echo e($role->name); ?>

                </option>
            <?php endif; ?>
        <?php else: ?>
            <option value="<?php echo e($role->name); ?>"
                <?php echo e(!empty($user) && $user->hasRole($role->name) ? 'selected' : ''); ?>>
                <?php echo e($role->name); ?>

            </option>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
                            <?php echo $errors->first('role', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label"><?php echo app('translator')->get('translation.FechaNacimiento'); ?></label>
                            <input type="text" id="duedate-input" class="form-control" placeholder="Select due date"
                                name="dob" data-date-format="dd M, yyyy" data-provide="datepicker"
                                data-date-autoclose="true"
                                value="<?php echo e(old('dob', $user->dob ?? \Carbon\Carbon::now()->format('Y-m-d'))); ?>">
                            <?php echo $errors->first('dob', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>'); ?>

                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
                                <input class="form-check-input" type="checkbox" id="active-switch" name="activo"
                                    value="1" <?php echo e(old('activo', $user->activo ?? true) ? 'checked' : ''); ?>>
                                <label class="form-check-label ms-2 mb-0" for="active-switch"><?php echo app('translator')->get('Activo'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">


                        <div class="mb-3">
                            <label class="form-label">Avatar</label>

                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute bottom-0 end-0">
                                        <label for="project-image-input" class="mb-0" data-bs-toggle="tooltip"
                                            data-bs-placement="right" aria-label="Select Image"
                                            data-bs-original-title="Select Image">
                                            <div class="avatar-xs">
                                                <div
                                                    class="avatar-title bg-light border rounded-circle text-muted cursor-pointer shadow font-size-16">
                                                    <i class="bx bxs-image-alt"></i>
                                                </div>
                                            </div>
                                        </label>
                                        <input name="avatar" class="form-control d-none" id="project-image-input"
                                            type="file" accept="image/png, image/gif, image/jpeg">
                                    </div>
                                    <div class="avatar-lg">
                                        <div class="avatar-title bg-light rounded-circle">
                                            <?php if(!empty($user) && $user->avatar): ?>
                                                <img src="<?php echo e(URL::asset('storage/avatares/' . $user->avatar)); ?>"
                                                    id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                            <?php else: ?>
                                                <img src="<?php echo e(URL::asset('build/images/default-avatar.png')); ?>"
                                                    id="projectlogo-img" class="avatar-md h-auto rounded-circle">
                                            <?php endif; ?>
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
                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input class="form-check-input" type="checkbox"
                                        id="permiso<?php echo e($permission->id); ?>" name="permissions[]"
                                        value="<?php echo e($permission->name); ?>"
                                        <?php echo e(!empty($user) && $user->hasPermissionTo($permission->name) ? 'checked' : ''); ?>>
                                    <label class="form-check-label"
                                        for="SwitchCheckSizemd"><?php echo e($permission->name); ?></label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <a href="<?php echo e(route('users.index')); ?>"class="btn btn-secondary"><?php echo app('translator')->get('botones.Volver'); ?></a>
            <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('botones.Guardar'); ?></button>
        </div>
    </div>
    <div class="col-lg-6">
        <?php if(!empty($user->id)): ?>
            <div class="text-end mb-4">
                <button type="button" class="btn btn-danger"
                    onclick="if(confirm('<?php echo e(__('messages.estas_seguro')); ?>')) { document.getElementById('delete-form').submit(); };"><?php echo app('translator')->get('botones.Eliminar'); ?></button>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/users/form.blade.php ENDPATH**/ ?>