

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Recover_Password'); ?> 2
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/owl.carousel/assets/owl.carousel.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/owl.carousel/assets/owl.theme.default.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body class="auth-body-bg">
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
        <div>
            <div class="container-fluid p-0">
                <div class="row g-0">

                    <div class="col-xl-9">
                        <div class="auth-full-bg pt-lg-5 p-4">
                            <div class="w-100">
                                <div class="bg-overlay"></div>
                                
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-3">
                        <div class="auth-full-page-content p-md-5 p-4">
                            <div class="w-100">

                                <div class="d-flex flex-column h-100">
                                    <div class="mb-4 mb-md-5">
                                        <a href="/index" class="d-block auth-logo">
                                            <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt=""
                                                height="18" class="auth-logo-dark">
                                            <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt=""
                                                height="18" class="auth-logo-light">
                                        </a>
                                    </div>
                                    <div class="my-auto">

                                        <div>
                                            <h5 class="text-primary">Restaurar Contraseña</h5>
                                            <p class="text-muted">Escribe el email con el que estas registrado para poder restaurar tu contraseña</p>
                                        </div>

                                        <div class="mt-4">
                                            <?php if(session('status')): ?>
                                                <div class="alert alert-success text-center mb-4" role="alert">
                                                    <?php echo e(session('status')); ?>

                                                </div>
                                            <?php endif; ?>
                                            <form class="form-horizontal" method="POST"
                                                action="<?php echo e(route('password.email')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email"
                                                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        id="useremail" name="email" placeholder="Introduce email"
                                                        value="<?php echo e(old('email')); ?>" id="email">
                                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong><?php echo e($message); ?></strong>
                                                        </span>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>

                                                <div class="text-end">
                                                    <button class="btn btn-primary w-md waves-effect waves-light"
                                                        type="submit">Restaurar</button>
                                                </div>

                                            </form>
                                            <div class="mt-5 text-center">
                                                <p>¿La has recordado? <a href="<?php echo e(url('login')); ?>"
                                                        class="font-weight-medium text-primary"> Valídate aquí</a> </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 mt-md-5 text-center">
                                        <p class="mb-0">©
                                            <script>
                                                document.write(new Date().getFullYear())
                                            </script> TE 2.O. Crafted with <i
                                                class="mdi mdi-heart text-danger"></i> by
                                            Taller Empresarial 2.0
                                        </p>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid -->
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('script'); ?>
        <!-- owl.carousel js -->
        <script src="<?php echo e(URL::asset('build/libs/owl.carousel/owl.carousel.min.js')); ?>"></script>
        <!-- auth-2-carousel init -->
        <script src="<?php echo e(URL::asset('build/js/pages/auth-2-carousel.init.js')); ?>"></script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master-without-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PROYECTOS\_SKOTE-LARAVEL-template\Starterkit\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>