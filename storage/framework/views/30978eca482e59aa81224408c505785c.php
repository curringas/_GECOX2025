<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<!-- Modal noticia -->
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

    <div class="modal fade" id="noticia" tabindex="-1" aria-labelledby="noticiaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="noticiaLabel">Noticia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí tu formulario de edición -->
                    <form method="POST" action="<?php echo e(route('noticia.guardar')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="noticiaTabla" name="noticiaTabla" value="">
                        <input type="hidden" id="noticiaOrden" name="noticiaOrden" value="">
                        <input type="hidden" id="noticiaIdentificador" name="noticiaIdentificador" value="">
                        <div class="mb-3">
                            <label for="noticiaPublicacion" class="form-label">Selecciona un publicacion</label>
                            <select class="form-select select2" id="noticiaPublicacion" name="noticiaPublicacion" required>
                                <option value="">-- Selecciona una publicación --</option>
                            </select>
                        </div>
                        <!-- Añade más campos según lo que quieras editar -->
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div><?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/portada/modal-noticia.blade.php ENDPATH**/ ?>