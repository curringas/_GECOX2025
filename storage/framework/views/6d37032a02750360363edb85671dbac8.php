<div class="card overflow-hidden p-2 text-center" style="overflow:hidden;">
    <!-- Botón editar arriba a la derecha -->
    <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
        
        <button type="button" 
                data-tabla="<?php echo e($tabla); ?>" 
                data-banner="<?php echo e($banner); ?>" 
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal" 
                data-bs-target="#banner">
            <i class="mdi mdi-pencil"></i> Editar
        </button>

        <button type="button" 
                data-tabla="<?php echo e($tabla); ?>" 
                data-eliminar="<?php echo e($banner); ?>" 
                class="btn btn-sm btn-danger">
                    <i class="mdi mdi-delete"></i>
        </button>
    </div>
    <?php if(isset($portada) && $codigo): ?>
        <?php echo e(str_replace(config('gecox_portada.banners.cabecera.ancho', '610'), config('gecox_portada.banners.cabecera.ancho', '728'), str_replace("105", "112", $codigo))); ?>

    <?php elseif(isset($portada) && $imagen): ?>
        <img src="<?php echo e(asset('storage/'.$imagen)); ?>" 
            alt="<?php echo e($titulo); ?>"
            title="<?php echo e($titulo); ?>" 
            width="<?php echo e($width); ?>"
            height="<?php echo e($height); ?>"
            class="img-fluid">
    <?php else: ?>
        <p>No hay banner asignado</p>
    <?php endif; ?>
</div><?php /**PATH /Users/curro/Developer/_GECOX2025/resources/views/portada/banner.blade.php ENDPATH**/ ?>