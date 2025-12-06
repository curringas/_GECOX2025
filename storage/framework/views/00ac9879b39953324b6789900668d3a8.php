<div class="card">
            <div class="card-header position-relative">
                <h4 class="card-title">&nbsp;</h4>
                <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                <!-- Noticia automática -->
                <button type="button"
                        data-tabla="<?php echo e($tabla); ?>"
                        data-banner="Banner"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#auto">
                    <i class="mdi mdi-plus"></i> Auto
                </button>
                <!-- Noticia -->
                <button type="button" 
                        data-tabla="<?php echo e($tabla); ?>"
                        data-banner="Banner"  
                        data-orden="nuevo"
                        data-id="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#noticia">
                    <i class="mdi mdi-plus"></i> Noticia
                </button>
                <!-- Banner -->
                <button type="button" 
                        data-tabla="<?php echo e($tabla); ?>" 
                        data-banner="Banner"  
                        data-id="nuevo"
                        data-orden="nuevo"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" 
                        data-bs-target="#banner">
                    <i class="mdi mdi-plus"></i>Banner
                </button>
                </div>
            </div>
            <div class="card-body lista-ordenable p-2" data-tabla="<?php echo e($tabla); ?>" id="lista_<?php echo e($tabla); ?>" style="overflow:hidden;">

                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('portada.item', ['clase' => $item,'tabla' => $tabla], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/portada/columna.blade.php ENDPATH**/ ?>