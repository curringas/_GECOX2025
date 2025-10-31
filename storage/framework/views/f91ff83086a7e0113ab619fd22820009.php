<li class="dd-item" data-id="<?php echo e($categoria->Identificador); ?>">
    <div class="dd-content d-flex justify-content-between align-items-center">
        <div class="dd-handle flex-grow-1" style="<?php echo e($categoria->Menu!=0 ? 'font-weight:normal' : ''); ?>">
            <?php echo e($categoria->Etiqueta ?? 'Sin título'); ?> 
            <?php echo e($categoria->Menu!=0 ? ' [Oculta]' : ''); ?>

        </div>
        <a href="<?php echo e(route('categorias.edit', $categoria->Identificador)); ?>" 
           class="btn btn-sm btn-outline-primary ms-2">
            Modificar
        </a>
    </div>

    <?php if($categoria->hijos && $categoria->hijos->count()): ?>
        <ol class="dd-list">
            <?php $__currentLoopData = $categoria->hijos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hijo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('categorias.partials.categoria-item', ['categoria' => $hijo], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
    <?php endif; ?>
</li>
<?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/categorias/partials/categoria-item.blade.php ENDPATH**/ ?>