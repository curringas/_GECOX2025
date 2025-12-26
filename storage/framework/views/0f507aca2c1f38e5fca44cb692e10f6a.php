<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('titulos.Editar_Usuario'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>    
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('titulos.Editar_Usuario'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form action="<?php echo e(route('indexado.update')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="card">
            <div class="card-header">
                <h4>Datos para SEO</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="Nombre" class="form-label">Nombre del sitio (Title)</label>
                    <input type="text" class="form-control" id="Nombre" name="Nombre" value="<?php echo e(old('Nombre', $indexado->Nombre)); ?>">
                </div>

                <div class="mb-3">
                    <label for="Descripcion" class="form-label">Descripción (Meta Description)</label>
                    <textarea class="form-control" id="Descripcion" name="Descripcion" rows="4"><?php echo e(old('Descripcion', $indexado->Descripcion)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Keywords" class="form-label">Keywords (Meta Keywords)</label>
                    <textarea class="form-control" id="Keywords" name="Keywords" rows="3"><?php echo e(old('Keywords', $indexado->Keywords)); ?></textarea>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Redes Sociales</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="Facebook" class="form-label">Facebook URL</label>
                    <input type="url" class="form-control" id="Facebook" name="Facebook" value="<?php echo e(old('Facebook', $indexado->Facebook)); ?>">
                </div>

                <div class="mb-3">
                    <label for="Twitter" class="form-label">Twitter URL</label>
                    <input type="url" class="form-control" id="Twitter" name="Twitter" value="<?php echo e(old('Twitter', $indexado->Twitter)); ?>">
                </div>

                <div class="mb-3">
                    <label for="Google" class="form-label">Google+ URL (obsoleto, opcional)</label>
                    <input type="url" class="form-control" id="Google" name="Google" value="<?php echo e(old('Google', $indexado->Google)); ?>">
                </div>

                <div class="mb-3">
                    <label for="Youtube" class="form-label">Youtube URL</label>
                    <input type="url" class="form-control" id="Youtube" name="Youtube" value="<?php echo e(old('Youtube', $indexado->Youtube)); ?>">
                </div>

                <div class="mb-3">
                    <label for="Instagram" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control" id="Instagram" name="Instagram" value="<?php echo e(old('Instagram', $indexado->Instagram)); ?>">
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Contadores (opcional)</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="ContadorFacebook" class="form-label">Contador Facebook</label>
                        <input type="number" class="form-control" id="ContadorFacebook" name="ContadorFacebook" value="<?php echo e(old('ContadorFacebook', $indexado->ContadorFacebook)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorTwitter" class="form-label">Contador Twitter</label>
                        <input type="number" class="form-control" id="ContadorTwitter" name="ContadorTwitter" value="<?php echo e(old('ContadorTwitter', $indexado->ContadorTwitter)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorInstagram" class="form-label">Contador Instagram</label>
                        <input type="number" class="form-control" id="ContadorInstagram" name="ContadorInstagram" value="<?php echo e(old('ContadorInstagram', $indexado->ContadorInstagram)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorTelegram" class="form-label">Contador Telegram</label>
                        <input type="number" class="form-control" id="ContadorTelegram" name="ContadorTelegram" value="<?php echo e(old('ContadorTelegram', $indexado->ContadorTelegram)); ?>">
                    </div>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary my-4">Guardar Cambios</button>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- bootstrap datepicker -->
    <script src="<?php echo e(URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')); ?>"></script>
    <!-- Idioma español del deatepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.es.min.js"></script>
    
    <script src="<?php echo e(URL::asset('build/libs/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/xpt_categorias.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/pages/xpt_seo.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/palabrea_publicaciones.init.js')); ?>"></script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script-bottom'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/indexado/edit.blade.php ENDPATH**/ ?>