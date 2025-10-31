<form method="POST" id="formCategoria" action="<?php echo e(route('categorias.store')); ?>"  role="form" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="Identificador" value="<?php echo e(isset($amodificar) ? $amodificar->Identificador : ''); ?>">
    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        <?php
            // Detectar tipo actual si estamos editando
            $tipoActual = null;

            if (isset($amodificar)) {
                if ($amodificar->Estatico) {
                    $tipoActual = 1;
                } elseif ($amodificar->SoloEtiqueta == 1) {
                    $tipoActual = 2;
                } elseif ($amodificar->Externo) {
                    $tipoActual = 3;
                } else {
                    $tipoActual = 0;
                }
            }

            // Tomar el valor del old() si hay validación fallida
            $tipoSeleccionado = old('Tipo', $tipoActual);
        ?>
        <select class="form-select" name="Tipo" id="tipo">
            <option value="0" <?php echo e($tipoSeleccionado == 0 ? 'selected' : ''); ?>>Con publicaciones</option>
            <option value="1" <?php echo e($tipoSeleccionado == 1 ? 'selected' : ''); ?>>Estática (Sólo soporte técnico)</option>
            <option value="2" <?php echo e($tipoSeleccionado == 2 ? 'selected' : ''); ?>>Etiqueta (Contenedor de submenús)</option>
            <option value="3" <?php echo e($tipoSeleccionado == 3 ? 'selected' : ''); ?>>Enlace a web externa</option>
        </select>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="etiqueta" class="form-label">Pertenece a</label>
            <select class="form-select" name="Padre" id="padre" required="">
                <option value="---" <?php echo e(old('Padre', $amodificar->Padre ?? null) == null ? 'selected'  : ''); ?>>
                    Inicio
                </option>
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($categoria->Identificador); ?>" 
                                        <?php echo e(old('Padre', $amodificar->Padre ?? '') == $categoria->Identificador  ? 'selected'  : ''); ?>>
                        <?php echo e($categoria->Etiqueta); ?>

                    </option>
                    <?php if($categoria->hijos && $categoria->hijos->count()): ?>
                        <?php $__currentLoopData = $categoria->hijos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hijo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($hijo->Identificador); ?>"
                                        <?php echo e(old('Padre', $amodificar->Padre ?? '') == $hijo->Identificador  ? 'selected' : ''); ?>>
                                &nbsp;> <?php echo e($hijo->Etiqueta); ?>

                            </option>
                            <?php if($hijo->hijos && $hijo->hijos->count()): ?>
                                <?php $__currentLoopData = $hijo->hijos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nieto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($nieto->Identificador); ?>" 
                                        <?php echo e(old('Padre', $amodificar->Padre ?? '') == $nieto->Identificador  ? 'selected' : ''); ?>>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>> <?php echo e($nieto->Etiqueta); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-6 mb-3 hideable-solo-etiqueta">
            <label for="orden" class="form-label">Privacidad</label>
            <select class="form-select" name="Privacidad" id="privacidad" required="">
                <option value="1" <?php echo e(old('Privacidad', $amodificar->Privacidad ?? '') == 1 ? 'selected' : ''); ?>>
                    Pública
                </option>
                <option value="2" <?php echo e(old('Privacidad', $amodificar->Privacidad ?? '') == 2 ? 'selected' : ''); ?>>
                    Nivel 2 - Registrados
                </option>   
                <option value="3" <?php echo e(old('Privacidad', $amodificar->Privacidad ?? '') == 3 ? 'selected' : ''); ?>>
                    Nivel 3 - Registrados ++
                </option>   
                <option value="4" <?php echo e(old('Privacidad', $amodificar->Privacidad ?? '') == 4 ? 'selected' : ''); ?>>
                    Nivel 4 - Registrados +++
                </option>   
                <option value="5" <?php echo e(old('Privacidad', $amodificar->Privacidad ?? '') == 5 ? 'selected' : ''); ?>>
                    Nivel 5 - Registrados ++++
                </option>   
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="etiqueta" class="form-label">Etiqueta* <span class="text-muted">(Nombre en el menú)</span></label>
            <input type="text" name="Etiqueta" class="form-control bg-warning-subtle" value="<?php echo e(old('Etiqueta', $amodificar->Etiqueta ?? '')); ?>" id="etiqueta" placeholder="Etiqueta" required>
            
        </div>
        <div class="col-md-6 mb-3 hideable-solo-etiqueta">
            <label for="url" class="form-label">Slug <span class="text-muted">(url amigable)</span></label>
            <input type="text" name="Url" class="form-control" value="<?php echo e(old('Url', $amodificar->Url ?? '')); ?>" id="url" placeholder="Slug">
        </div>
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="titulo" class="form-label">Título <span class="text-muted">(Titulo h1 de la página obligatorio para publicaciones o estática)</span></label>
        <input type="text" name="Titulo" class="form-control " value="<?php echo e(old('Titulo', $amodificar->Titulo ?? '')); ?>" id="titulo" placeholder="Titulo">
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="explicativo" class="form-label">Descripción superior</label>
        <textarea id="explicativo" class="form-control" rows="3" name="Explicativo" placeholder="Introduce una descripción corta para la parte superior de la sección">
            <?php echo e(old('Explicativo', $amodificar->Explicativo ?? '')); ?>

        </textarea>
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="explicativoproductos" class="form-label">Descripción inferior</label>
        <textarea id="explicativoproductos" class="form-control" rows="3" name="ExplicativoProductos" placeholder="Introduce una descripción larga para la parte inferior de la sección">
            <?php echo e(old('ExplicativoProductos', $amodificar->ExplicativoProductos ?? '')); ?>

        </textarea>
    </div>
    
    
    <div class="mb-3 hideable-link-external" style="display: none;">
        <label for="externo" class="form-label">Enlace externo</label>
        <input type="text" name="Externo" class="form-control " value="<?php echo e(old('Externo', $amodificar->Externo ?? '')); ?>" id="externo" placeholder="https://">
        <?php $__errorArgs = ['Externo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="text-danger small"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="mb-3 hideable-link-external" style="display: none;">
        <label for="target" class="form-label">Abrir en</label>
        <select class="form-select" name="Target" id="target" required="">
            <option value="0" <?php echo e(old('Target', $amodificar->Target ?? 0) == 0 ? 'selected' : ''); ?>>
                En la misma ventana
            </option>
            <option value="1" <?php echo e(old('Target', $amodificar->Target ?? 0) == 1 ? 'selected' : ''); ?>>
                En una nueva ventana
            </option>
        </select>
    </div>
    <div class="mb-3 hideable-estatico" style="display: none;">
        <label for="estatico" class="form-label">Archivo estático</label>
        <input type="text" name="Estatico" class="form-control " value="<?php echo e(old('Estatico', $amodificar->Estatico ?? '')); ?>" id="estatico" placeholder="archivo.html">
    </div>
    <div class="mb-3">
        <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
            <input class="form-check-input" type="checkbox" id="active-switch" name="Menu" value="0" <?php echo e(old('Menu', $amodificar->Menu ?? 0) == 0 ? 'checked="checked"' : ''); ?>>
            <label class="form-check-label ms-2 mb-0" for="active-switch">Visible</label>
        </div>
    </div>
    <div class="p-3 mb-3 background-seo hideable-solo-etiqueta">
        <h4 class="font-size-16 mb-3">Opciones posicionamiento SEO</h4>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-muted">(60 carateres aprox.)</span></label>
            <input type="text" id="MetaTitle" class="form-control" maxlength="70" value=" <?php echo e($amodificar->MetaTitle ?? ''); ?>" placeholder="Introduce un titulo similar diferente" name="MetaTitle">
            <small class="text-muted position-absolute end-0 me-4" id="titleCount">
                0 / 70
            </small>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Description <span class="text-muted">(160 carateres aprox.)</span></label>
            <textarea id="MetaDescription"  class="form-control" maxlength="200" rows="3" name="MetaDescription" placeholder="Introduce una descripción para motores de búsqueda"><?php echo e($amodificar->MetaDescription ?? ''); ?></textarea>
            <small class="text-muted position-absolute end-0 me-4" id="descCount">
                0 / 170
            </small>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
</form>

<?php $__env->startSection('script-bottom'); ?>

    <script src="<?php echo e(URL::asset('build/js/pages/xpt_seo.init.js')); ?>"></script>

<script>

    
$(document).ready(function() {
    function actualizarCamposPorTipo() {
        const tipo = parseInt($('#tipo').val());

        // Ocultar todos los grupos dependientes
       // Primero mostrar todo por defecto
        $('.hideable-link-external').hide();
        $('.hideable-estatico').hide();
        $('.hideable-solo-etiqueta').show();

        // Mostrar según tipo
        if (tipo === 1) {
            $('.hideable-estatico').show();
        } else if (tipo === 3) {
            $('.hideable-solo-etiqueta').hide();
            $('.hideable-link-external').show();
        } else if (tipo === 2) {
            $('.hideable-solo-etiqueta').hide(); // Si más adelante hay secciones especiales para etiquetas
        }
        // tipo 0 → no se hace nada
    }

    // Llamar al cargar (para mantener coherencia al editar)
    actualizarCamposPorTipo();

    // Llamar cada vez que se cambia el tipo
    $('#tipo').on('change', function() {
        actualizarCamposPorTipo();
    });

    $('#formCategoria').on('submit', function() {
        // Antes de enviar el formulario, asegurarse de que los campos ocultos no se envían
        $('.hideable-link-external input, .hideable-estatico input').each(function() {
            if ($(this).closest('.hideable-link-external, .hideable-estatico').is(':hidden')) {
                $(this).val(''); // Limpiar el valor para que no se envíe
            }
        });
    });


    // Inicializar TinyMCE para el textarea explicativo WYSYIWYG
    tinymce.init({
        selector: 'textarea#explicativo, textarea#explicativoproductos',
        height: 350,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:"Poppins",sans-serif; font-size:16px }'
    });


    
});
</script>
<?php $__env->stopSection(); ?><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/categorias/partials/categoria-form.blade.php ENDPATH**/ ?>