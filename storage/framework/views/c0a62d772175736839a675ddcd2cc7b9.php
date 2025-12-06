<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.Dashboards'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php $__env->startComponent('components.breadcrumb'); ?>
<?php $__env->slot('li_1'); ?> Portada <?php $__env->endSlot(); ?>
<?php $__env->slot('title'); ?> Portada <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header position-relative">
                <h4 class="card-title">Banner Sobrecabecera</h4>
                <button type="button" 
                        data-tabla="portada_slider" 
                        data-banner="Banner"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary position-absolute top-0 end-0 m-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-plus"></i> Nuevo banner
                </button>
            </div>
            <div class="card-body lista-ordenable" data-tabla="portada_slider" id="listaOrdenableBannersSobrecabecera" style="overflow:hidden;">

                <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-4 position-relative" data-id="<?php echo e($slider->Identificador); ?>">
                        <!-- Botón editar arriba a la derecha -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                            
                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-banner="Banner"  
                                    data-id="<?php echo e($slider->Identificador); ?>" 
                                    data-orden="<?php echo e($slider->Orden); ?>" 
                                    class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#banner">
                                <i class="mdi mdi-pencil"></i> Editar
                            </button>

                            <button type="button" 
                                    data-tabla="portada_slider" 
                                    data-banner-eliminar="Banner" 
                                    data-id="<?php echo e($slider->Identificador); ?>" 
                                    data-orden="<?php echo e($slider->Orden); ?>" 
                                    class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                            <?php if($slider->BannerCodigoFuente): ?>
                                <?php echo e($slider->BannerCodigoFuente); ?>

                            <?php elseif(isset($slider) && $slider->BannerImagen): ?>
                                <img src="<?php echo e(asset('storage/'.$slider->BannerImagen)); ?>" 
                                    alt="<?php echo e($slider->BannerTitulo); ?>"
                                    title="<?php echo e($slider->BannerTitulo); ?>" 
                                    width="<?php echo e(config('gecox_portada.banners.slider.ancho', '1080')); ?>"
                                    height="<?php echo e(config('gecox_portada.banners.slider.alto', '150')); ?>"
                                    class="img-fluid">
                            <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4 d-flex">
        <div class="card overflow-hidden p-2 text-center w-100">
            <img src="<?php echo e(URL::asset('build/images/logo-light.png')); ?>" alt="" class="img-fluid" width="291" height="90">
        </div>
    </div>
    <div class="col-xl-8 d-flex">
        <div class="card overflow-hidden p-2 text-center" style="overflow:hidden;">
            <!-- Botón editar arriba a la derecha -->
            <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                
                <button type="button" 
                        data-tabla="portada" 
                        data-banner="banner_cabecera" 
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-pencil"></i> Editar
                </button>

                <button type="button" 
                        data-tabla="portada" 
                        data-banner-eliminar="banner_cabecera" 
                        class="btn btn-sm btn-danger">
                         <i class="mdi mdi-delete"></i>
                </button>
            </div>

            <?php if(isset($portada) && $portada->banner_cabeceraCodigoFuente): ?>
                <?php echo e(str_replace(config('gecox_portada.banners.cabecera.ancho', '610'), config('gecox_portada.banners.cabecera.ancho', '728'), str_replace("105", "112", $portada->banner_cabeceraCodigoFuente))); ?>

            <?php elseif(isset($portada) && $portada->banner_cabeceraImagen): ?>
                <img src="<?php echo e(asset('storage/'.$portada->banner_cabeceraImagen)); ?>" 
                    alt="<?php echo e($portada->banner_cabeceraTitulo); ?>"
                    title="<?php echo e($portada->banner_cabeceraTitulo); ?>" 
                    width="<?php echo e(config('gecox_portada.banners.cabecera.ancho', '728')); ?>"
                    height="<?php echo e(config('gecox_portada.banners.cabecera.alto', '90')); ?>"
                    class="img-fluid">
            <?php endif; ?>
        </div>
    </div>
</div>

<!--MENU DE NAVEGACION -->
<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card bg-dark text-white">
            <div class="card-body">
                Esta es la zona de navegacion 
                    <button type="button" class="btn btn-secondary btn-sm ms-2">
                        <a href="<?php echo e(route('categorias.index')); ?>" class="text-white">
                            Configurar Menu
                        </a>
                    </button>
            </div>
        </div>
    </div>
</div>


<!-- CONTENIDO DE LA PORTADA -->
<div class="row mb-3">

    <!-- IZQUIERDA --->
    <div class="col-xl-6">
        <?php echo $__env->make('portada.columna', ['items' => $izquierdos,'tabla' => 'portada_izquierda'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    
    <!-- CENTRO --->
    <div class="col-xl-3">
        <?php echo $__env->make('portada.columna', ['items' => $centrales,'tabla' => 'portada_central'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>


    <!-- DERECHA --->
    <div class="col-xl-3">
        <?php echo $__env->make('portada.columna', ['items' => $derechos,'tabla' => 'portada_derecha'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    

    
</div>


<?php echo $__env->make('portada.modal-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('portada.modal-noticia', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>

    <!-- gecox_portada -->
    <script>
            window.csrfToken = "<?php echo e(csrf_token()); ?>";
            window.bannerDatosUrl = "<?php echo e(route('banner.datos')); ?>";
            window.bannerEliminarUrl = "<?php echo e(route('banner.eliminar')); ?>";
            window.reordenarUrl = "<?php echo e(route('portada.reordenar')); ?>";
    </script>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/pages/gecox_portada.init.js'); ?>

    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/index.blade.php ENDPATH**/ ?>