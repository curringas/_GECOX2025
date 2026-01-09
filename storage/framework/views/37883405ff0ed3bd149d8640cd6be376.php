<?php echo csrf_field(); ?>
<input type="hidden" name="Identificador" value="<?php echo e($banner->Identificador ?? ''); ?>">

<div class="row">
    <div class="col-md-8">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Datos banner</h4>

            <div class="mb-3">
                <label class="form-label">Título <span class="text-danger">*</span></label>
                <input name="Titulo" value="<?php echo e(old('Titulo', $banner->Titulo ?? '')); ?>" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen (Banner)</label>
                <input type="file" name="Banner" class="form-control mt-2" />
                <input type="hidden" name="old_Banner" value="<?php echo e($banner->Banner ?? ''); ?>" />
                <input type="hidden" name="remove_banner" id="remove_banner" value="0" />
            </div>

            <div class="mb-3">
                <label class="form-label">ImagenMovil (Banner para versión móvil)</label>
                <input type="file" name="BannerMovil" class="form-control mt-2" />
                <input type="hidden" name="old_BannerMovil" value="<?php echo e($banner->BannerMovil ?? ''); ?>" />
            </div>

            <div class="mb-3">
                <label class="form-label">URL</label>
                <input name="URL" value="<?php echo e(old('URL', $banner->URL ?? '')); ?>" class="form-control" />
            </div>

            <div class="mb-3">
                <label class="form-label">Código (HTML)</label>
                <textarea name="Codigo" rows="10" class="form-control"><?php echo e(old('Codigo', $banner->Codigo ?? '')); ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Guardar</button>
            </div>
        </div></div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">

            <h4 class="card-title">Metadatos</h4>
            <p><?php echo e(isset($banner) ? 'Ultima modificación el ' . \Carbon\Carbon::parse($banner->Fecha)->format('d/m/Y H:i') : ''); ?>

                <br>
                <?php echo e(isset($banner) ? 'Creado por ' . $banner->Creador : ''); ?>

            </p>

            <div class="mb-3">
                <label class="form-label">Tipo / Zona</label>
                <select name="Tipo" class="form-select" required>
                    <option value="">Seleciona Tipo/Zona</option>
                    <option value="0" <?php echo e(old('Tipo', $banner->Tipo ?? '') == 1 ? 'selected' : ''); ?>><?php echo e(config('gecox_banners.tipos.0')); ?></option>
                    <option value="1" <?php echo e(old('Tipo', $banner->Tipo ?? '') == 1 ? 'selected' : ''); ?>><?php echo e(config('gecox_banners.tipos.1')); ?></option>
                    <option value="2" <?php echo e(old('Tipo', $banner->Tipo ?? '') == 2 ? 'selected' : ''); ?>><?php echo e(config('gecox_banners.tipos.2')); ?></option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Target</label>
                <select name="Target" class="form-select mb-2">
                    <option value="1" <?php echo e(old('Target', $banner->Target ?? '') == 1 ? 'selected' : ''); ?>><?php echo e(config('gecox_banners.targets.1')); ?></option>
                    <option value="0" <?php echo e(old('Target', $banner->Target ?? '') == 0 ? 'selected' : ''); ?>><?php echo e(config('gecox_banners.targets.0')); ?></option>
                </select>
            </div>

            <hr>
            <h5>Categorías</h5>
            <?php
                $selectedIds = old('categorias', isset($banner) ? $banner->categorias->pluck('Identificador')->toArray() : []);
            ?>
            <select name="categorias[]" id="categorias" class="form-select select2" multiple>         
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($categoria->Identificador); ?>"
                        <?php echo e(in_array($categoria->Identificador, $selectedIds) ? 'selected' : ''); ?>>
                        <?php echo e($categoria->Etiqueta); ?>

                    </option>

                    <?php if($categoria->hijos && $categoria->hijos->count()): ?>
                        <?php $__currentLoopData = $categoria->hijos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subcategoria->Identificador); ?>"
                                <?php echo e(in_array($subcategoria->Identificador, $selectedIds) ? 'selected' : ''); ?>>
                                ---<?php echo e($subcategoria->Etiqueta); ?>

                            </option>

                            <?php if($subcategoria->hijos && $subcategoria->hijos->count()): ?>
                                <?php $__currentLoopData = $subcategoria->hijos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supracategoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supracategoria->Identificador); ?>"
                                        <?php echo e(in_array($supracategoria->Identificador, $selectedIds) ? 'selected' : ''); ?>>
                                        -----<?php echo e($supracategoria->Etiqueta); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['categorias'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if(isset($banner)): ?>
                <div class="mb-3 mt-3" id="current-banner-preview">
                    <div class="position-relative">
                        <?php if($banner->Codigo): ?>
                            <?php echo $banner->Codigo; ?>

                        <?php else: ?>
                            <a href="<?php echo e($banner->URL ? $banner->URL : '#'); ?>" target="_blank">
                                <img src="<?php echo e(asset('storage/banners/' . $banner->Banner)); ?>" alt="<?php echo e($banner->Titulo); ?>" class="img-fluid" />
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="delete-current-banner" title="Eliminar imagen">&times;</button>
                    </div>
                    <small id="remove-banner-note" class="text-muted ms-2" style="display:none;">Imagen marcada para eliminación</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const btn = document.getElementById('delete-current-banner');
    console.log(btn);
    if (!btn) return;
    btn.addEventListener('click', function(){
        if (!confirm('¿Eliminar la imagen actual?')) return;
        const oldInput = document.querySelector('input[name="old_Banner"]');
        const oldInputMovil = document.querySelector('input[name="old_BannerMovil"]');
        if (oldInput) oldInput.value = '';
        if (oldInputMovil) oldInputMovil.value = '';
        const removeInput = document.getElementById('remove_banner');
        if (removeInput) removeInput.value = '1';
        const preview = document.getElementById('current-banner-preview');
        if (preview) preview.style.display = 'none';
        const note = document.getElementById('remove-banner-note');
        if (note) note.style.display = 'inline';
    });
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH /Users/curro/Developer/_GECOX2025/resources/views/banners/form.blade.php ENDPATH**/ ?>