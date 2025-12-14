<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<!-- Modal banner -->
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

    <div class="modal fade" id="banner" tabindex="-1" aria-labelledby="bannerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerLabel">Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí tu formulario de edición -->
                    <form method="POST" action="{{ route('banner.guardar') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="bannerBanner" name="bannerBanner" value="">
                        <input type="hidden" id="bannerTabla" name="bannerTabla" value="">
                        <input type="hidden" id="bannerOrden" name="bannerOrden" value="">
                        <input type="hidden" id="bannerIdentificador" name="bannerIdentificador" value="">
                        <div class="mb-3">
                            <label for="bannerTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" name="bannerTitulo" id="bannerTitulo" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="bannerImagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" name="bannerImagen" id="bannerImagen">
                        </div>
                        <div class="mb-3">
                            <label for="bannerImagen" class="form-label">Imagen Movil</label>
                            <input type="file" class="form-control" name="bannerImagenMovil" id="bannerImagenMovil">
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="bannerUrl" class="form-label">Url de destino</label>
                                    <input type="text" class="form-control" name="bannerUrl" id="bannerUrl">
                                </div>
                                <div class="col-md-4">
                                    <label for="bannerDestino" class="form-label">Abrir en</label>
                                    <select class="form-select" id="bannerDestino" name="bannerDestino">
                                        <option value="_self">Misma ventana</option>
                                        <option value="_blank">Nueva ventana</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bannerCodigo" class="form-label">Código fuente <span class="text-warning">(Este código tendra preferencia sobre la imagen subida)</span></label>
                            <textarea class="form-control" name="bannerCodigo" id="bannerCodigo" rows="4"></textarea>
                        </div>
                        <!-- Añade más campos según lo que quieras editar -->
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>