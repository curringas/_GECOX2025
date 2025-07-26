

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Maintenance'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>

        <div class="home-btn d-none d-sm-block">
            <a href="index" class="text-dark"><i class="fas fa-home h2"></i></a>
        </div>

        <section class="my-5 pt-sm-5">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="home-wrapper">
                            <div class="mb-5">
                                <a href="index" class="d-block auth-logo">
                                    <img src="<?php echo e(URL::asset('build/images/logo-dark.png')); ?>" alt="" height="20"
                                        class="auth-logo-dark mx-auto">
                                    <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="" height="20"
                                        class="auth-logo-light mx-auto">
                                </a>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-sm-4">
                                    <div class="maintenance-img">
                                        <img src="<?php echo e(URL::asset('build/images/maintenance.svg')); ?>" alt="" class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                            <h3 class="mt-5">Aplicación en mantenimiento</h3>
                            <p>Por favor, inténtelo de nuevo más tarde.</p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mt-4 maintenance-box">
                                        <div class="card-body">
                                            <i class="bx bx-broadcast mb-4 h1 text-primary"></i>
                                            <h5 class="font-size-15 text-uppercase">¿Por qué este sitio no esta disponible?</h5>
                                            <p class="text-muted mb-0">Hay muchas causas posibles, pero la más probable es porque se este haciendo una mejora importante</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mt-4 maintenance-box">
                                        <div class="card-body">
                                            <i class="bx bx-time-five mb-4 h1 text-primary"></i>
                                            <h5 class="font-size-15 text-uppercase">
                                                ¿Cuanto tiempo he de esperar?</h5>
                                            <p class="text-muted mb-0">Normalmente este sitio cuando esta en mantenimiento no suele tardar mas de 1 hora en volver a estar operativo.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mt-4 maintenance-box">
                                        <div class="card-body">
                                            <i class="bx bx-envelope mb-4 h1 text-primary"></i>
                                            <h5 class="font-size-15 text-uppercase">
                                                ¿Necesitas ayuda?</h5>
                                            <p class="text-muted mb-0">Si necesitas ayuda o hacer cualquier consulta no dudes en contactar con el servicio técnico en <a
                                                    href="mailto:informatica@tallerempresarial.es"
                                                    class="text-decoration-underline">informatica@tallerempresarial.es</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master-without-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PROYECTOS\_SKOTE-LARAVEL-template\Starterkit\resources\views/mantenimiento/maintenance.blade.php ENDPATH**/ ?>