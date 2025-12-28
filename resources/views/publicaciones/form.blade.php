<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Contenido principal</h4>
                
                <input type="hidden" name="Identificador" value="{{ $publicacion->Identificador ?? '' }}">

                <div class="mb-3 justify-content-end d-flex align-items-center">
                    <div class="col-md-4 me-3">
                        <select class="form-select" name="Privacidad" id="privacidad" required="">
                            <option value="1" {{ old('Privacidad', $amodificar->Privacidad ?? '') == 1 ? 'selected' : '' }}>
                                Pública
                            </option>
                            <option value="2" {{ old('Privacidad', $amodificar->Privacidad ?? '') == 2 ? 'selected' : '' }}>
                                Nivel 2 - Registrados
                            </option>   
                            <option value="3" {{ old('Privacidad', $amodificar->Privacidad ?? '') == 3 ? 'selected' : '' }}>
                                Nivel 3 - Registrados ++
                            </option>   
                            <option value="4" {{ old('Privacidad', $amodificar->Privacidad ?? '') == 4 ? 'selected' : '' }}>
                                Nivel 4 - Registrados +++
                            </option>   
                            <option value="5" {{ old('Privacidad', $amodificar->Privacidad ?? '') == 5 ? 'selected' : '' }}>
                                Nivel 5 - Registrados ++++
                            </option>   
                        </select>
                    </div>
                    <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
                        <label class="form-check-label ms-2 mb-0" for="active-switch">Visible</label>
                        <input class="form-check-input" type="checkbox" id="active-switch" name="Activa" value="1" {{ old('Menu', $amodificar->Menu ?? 0) == 0 ? 'checked="checked"' : '' }}>
                        
                    </div>
                </div>

                {{-- TÍTULO --}}
                <div class="col-md-12 mb-3">
                    <label for="Titulo" class="form-label">Título <span class="text-danger">*</span></label>
                    <input type="text" name="Titulo" id="Titulo"
                            value="{{ old('Titulo', $publicacion->Titulo ?? '') }}"
                            class="form-control @error('Titulo') is-invalid @enderror" required>
                    <div class="invalid-feedback">Por favor, introduce el título.
                        @error('Titulo')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                {{-- SUBTÍTULO --}}
                <div class="col-md-12 mb-3">
                    <label for="Titulo" class="form-label">Subtítulo</label>
                    <input type="text" name="Subtitulo" id="Subtitulo"
                            value="{{ old('Subtitulo', $publicacion->Subtitulo ?? '') }}"
                            class="form-control @error('Subtitulo') is-invalid @enderror">
                    @error('Subtitulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SEO --}}
                <div class="p-3 mb-3 background-seo hideable-solo-etiqueta">
                    <h4 class="font-size-16 mb-3">Opciones posicionamiento SEO</h4>
                    <div class="mb-3">
                        <label for="Url" class="form-label">Slug <span class="text-muted">(url de la noticia,parecida al titulo. Usa - en lugar de espacios)</span></label>
                        <input type="text" id="Url" class="form-control" value="{{ old('Url', $publicacion->Url ?? '') }}" placeholder="Url de la noticia" name="Url">
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-muted">(60 carateres aprox.)</span></label>
                        <input type="text" id="MetaTitle" class="form-control" maxlength="70" value="{{ old('MetaTitle', $publicacion->MetaTitle ?? '') }}" placeholder="Introduce un titulo similar diferente" name="MetaTitle">
                        <small class="text-muted position-absolute end-0 me-4" id="titleCount">
                            0 / 70
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Description <span class="text-muted">(160 carateres aprox.)</span></label>
                        <textarea id="MetaDescription"  class="form-control" maxlength="200" rows="3" name="MetaDescription" placeholder="Introduce una descripción para motores de búsqueda">{{ old('MetaDescription', $publicacion->MetaDescription ?? '') }}</textarea>
                        <small class="text-muted position-absolute end-0 me-4" id="descCount">
                            0 / 170
                        </small>
                    </div>
                </div>

    
                <div class="mb-3 hideable-solo-etiqueta">
                    <label for="Introduccion" class="form-label">Contenido principal</label>
                    <textarea id="Introduccion" class="form-control" rows="3" name="Introduccion" placeholder="Introduce una descripción corta para la parte superior de la sección">
                        {{ old('Introduccion', $publicacion->Introduccion ?? '') }}
                    </textarea>
                </div>
                <div class="mb-3 hideable-solo-etiqueta">
                    <label for="Contenido" class="form-label">Contenido extenso</label>
                    <textarea id="Contenido" class="form-control" rows="3" name="Contenido" placeholder="Introduce una descripción larga para la parte inferior de la sección">
                        {{ old('Contenido', $publicacion->Contenido ?? '') }}
                    </textarea>
                </div>

                

                {{-- BOTONES --}}
                <div class="col-12 d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Autor y Fecha</h4>
                <div class="row mb-3">
                    {{-- AUTOR --}}
                    <div class="col-md-7">
                        <label for="Autor" class="form-label">Autor</label>
                        <input type="text" name="Autor" id="Autor"
                                value="{{ old('Autor', $publicacion->Autor ?? '') }}"
                                class="form-control @error('Autor') is-invalid @enderror">
                        @error('Autor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    {{-- FECHA --}}
                    <div class="col-md-5">
                        <label for="Fecha" class="form-label">Fecha</label>
                        <input type="date" name="Fecha" id="Fecha"
                                value="{{ old('Fecha', $publicacion?->Fecha ? \Carbon\Carbon::parse($publicacion->Fecha)->format('Y-m-d') : date("Y-m-d")) }}"
                                class="form-control @error('Fecha') is-invalid @enderror">
                        @error('Fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">

                    {{-- AUTOR --}}
                    <div class="col-md-12">
                        <label for="AutorTwitter" class="form-label">Autor Twitter</label>
                        <input type="text" name="AutorTwitter" id="AutorTwitter"
                                value="{{ old('AutorTwitter', $publicacion->AutorTwitter ?? '') }}"
                                class="form-control @error('AutorTwitter') is-invalid @enderror">
                        @error('AutorTwitter')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    {{-- EMAIL --}}
                    <div class="col-md-12">
                        <label for="AutorEmail" class="form-label">Email</label>
                        <input type="email" name="AutorEmail" id="AutorEmail"
                                value="{{ old('Fecha', $publicacion->AutorEmail ?? '') }}"
                                class="form-control @error('Fecha') is-invalid @enderror">
                        @error('Fecha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h4 class="card-title">Categorias</h4>

                {{-- CATEGORÍAS --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="categorias" class="form-label">Selecciona una o varias</label>
                         @php
                            $selectedIds = old(
                                'categorias',
                                $publicacion?->categorias ? $publicacion->categorias->pluck('Identificador')->toArray() : []
                            );
                        @endphp
                        <select name="categorias[]" id="categorias" class="form-select select2" multiple>
                           

                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->Identificador }}"
                                    {{ in_array($categoria->Identificador, $selectedIds) ? 'selected' : '' }}>
                                    {{ $categoria->Etiqueta }}
                                </option>

                                @if ($categoria->hijos && $categoria->hijos->count())
                                    @foreach ($categoria->hijos as $subcategoria)
                                        <option value="{{ $subcategoria->Identificador }}"
                                            {{ in_array($subcategoria->Identificador, $selectedIds) ? 'selected' : '' }}>
                                            ---{{ $subcategoria->Etiqueta }}
                                        </option>

                                        @if ($subcategoria->hijos && $subcategoria->hijos->count())
                                            @foreach ($subcategoria->hijos as $supracategoria)
                                                <option value="{{ $supracategoria->Identificador }}"
                                                    {{ in_array($supracategoria->Identificador, $selectedIds) ? 'selected' : '' }}>
                                                    -----{{ $supracategoria->Etiqueta }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif

                            @endforeach
                        </select>
                        @error('categorias')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <hr>
                <h4 class="card-title">Imágenes</h4>

                <div class="row mb-3">
                    


                    {{-- IMAGEN DESTACADA --}}
                    <div class="col-md-12">
                        <label for="ImagenDestacada" class="form-label">Añadir imagen</label>
                        <div class="mb-3">
                            <input type="file" class="form-control" id="inputGroupFile04" name="imagenes[]" multiple accept="image/*">
                        </div>
                        @php
                            if (isset($publicacion) && $publicacion->imagenes->count()) {
                                //  Obtener las descripciones de las imágenes existentes en orden
                                $existingDescriptions = $publicacion->imagenes
                                    // Mapear solo el campo 'Descripcion' de cada imagen
                                    ->pluck('Descripcion')
                                    // Unir las descripciones en una sola cadena, separadas por un salto de línea
                                    ->implode("\n"); 
                                // Generar las claves compuestas 'Publicacion|Orden'
                                $compositeKeys = $publicacion->imagenes
                                    ->map(fn($img) => $img->Publicacion . '|' . $img->Orden)
                                    ->implode(','); 
                            } else {
                                $existingDescriptions = '';
                                $compositeKeys = '';
                            }
                        @endphp

                        <input type="hidden" name="existing_image_keys" value="{{ $compositeKeys }}">

                        <div class="mb-3" id="description-area">
                            <label for="descripciones" class="form-label">Descripciones de Imágenes</label>
                            <textarea class="form-control" name="descripciones" id="descripciones" rows="5" 
                                    placeholder="...">{{ $existingDescriptions }}</textarea>
                            <small class="form-text text-muted">Asegúrate de mantener el orden para actualizar la descripción correcta.</small>
                        </div>

                        <div id="previewContainer" class="mt-2 d-flex flex-wrap gap-2">
                            @if(isset($publicacion) && $publicacion->imagenes->count())
                                @foreach ($publicacion->imagenes as $img)
                                    @php
                                        // Generamos la clave compuesta
                                        $compositeKey = $img->Publicacion . '|' . $img->Orden;

                                    @endphp
                                    <div class="position-relative d-inline-block existing-img" 
                                        data-id="{{ $compositeKey }}" 
                                        style="width:120px">
                                        <img src="{{ $img->thumb_url }}" class="img-thumbnail" style="width:100%; height:100px; object-fit:cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-existing-img">&times;</button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <input type="hidden" id="imagenes_a_eliminar" name="imagenes_a_eliminar">
                    </div>


                </div>

                <hr>
                <h4 class="card-title">Galería</h4>

                <div class="row mb-3">

                    <div class="col-md-12">
                        <label for="GaleriaURL" class="form-label">Galería externa (widget)</label>
                        <br>
                        Facebook: <i class="fa fa-info-circle text-info"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="<ol>
                                                <li>Haz click en link</li>
                                                <li>Pega la url de la galeria en el campo HREF</li>
                                                <li>Deja vacio el campo WIDTH</li>
                                                <li>Haz click en OBTENER CODIGO</li>
                                                <li>Copia el codigo y pégalo en este campo</li>
                                            </ol">
                            </i> =>
                            <span class="text-muted"><a href="https://developers.facebook.com/docs/plugins/embedded-posts" style="text-decoration: underline">Generar código</a></span>
                            <br><br>
                        <textarea name="GaleriaURL" id="GaleriaURL" rows="4"
                            class="form-control @error('GaleriaURL') is-invalid @enderror">{{ old('GaleriaURL', $publicacion->GaleriaURL ?? '') }}</textarea>
                        @error('GaleriaURL')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr>
                <h4 class="card-title">Video</h4>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="VideosURL" class="form-label">Video externo (URL)
                            <i class="fa fa-info-circle text-info"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Dailymotion (https://www.dailymotion.com/video/x8ek5lr<hr>
Dailymotion Compartir (https://dai.ly/x8enaax<hr>
Youtube<br>
- https://www.youtube.com/embed/x9I_wf0nSwU?si=gB-cnT4P_NFSypFU<br>
- https://youtu.be/x9I_wf0nSwU?si=7eNmK--0XvI4HDwP<br>
- https://www.youtube.com/watch?v=x9I_wf0nSwU&t=1s">
                            </i>
                        </label>
                        <input type="text" name="Video" id="Video"
                                value="{{ old('Video', $publicacion->Video ?? '') }}"
                                class="form-control @error('Video') is-invalid @enderror">
                        @error('Video')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror   
                    </div>
                </div>
                <hr>
                <h4 class="card-title">Documentos</h4>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="DocumentoAdjunto" class="form-label">Añadir documento</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="inputGroupFileDoc" 
                                name="documentos[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
                            
                        </div>
                        <div id="docPreviewContainer" class="mt-2">
                            {{-- 1. ITERAR sobre los documentos existentes en la publicación --}}
                            @if(isset($publicacion) && $publicacion->documentos->count())
                                <h5>Documentos actuales:</h5>
                                @foreach ($publicacion->documentos as $doc)
                                    @php
                                        // Determinar clases de icono y color (Mismo switch que en JS)
                                        $ext = pathinfo($doc->Documento, PATHINFO_EXTENSION);
                                        $iconClass = 'fa-file';
                                        $colorClass = 'text-secondary';
                                        switch ($ext) {
                                            case 'pdf': $iconClass = 'fa-file-pdf'; $colorClass = 'text-danger'; break;
                                            case 'doc':
                                            case 'docx': $iconClass = 'fa-file-word'; $colorClass = 'text-primary'; break;
                                            // ... (Añadir el resto de casos) ...
                                            case 'txt': $iconClass = 'fa-file-lines'; $colorClass = 'text-info'; break;
                                        }
                                        // Clave compuesta para identificar el documento
                                        $compositeKey = $doc->Publicacion . '|' . $doc->Orden;
                                    @endphp
                                    <div class="d-flex align-items-center mb-1 border p-2 rounded existing-doc" 
                                        data-id="{{ $compositeKey }}" 
                                        style="max-width: 400px;">
                                        <i class="fa {{ $iconClass }} me-2 {{ $colorClass }}" style="font-size: 1.3rem;"></i>
                                        <span class="flex-grow-1 text-truncate">{{ $doc->Nombre }}</span>
                                        {{-- El botón ahora llama a la función de eliminación JS --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 delete-existing-doc">&times;</button>
                                    </div>
                                @endforeach
                                {{-- 2. CAMPO OCULTO para registrar los IDs de los documentos a eliminar --}}
                                <input type="hidden" id="documentos_a_eliminar" name="documentos_a_eliminar">
                            @endif
                        </div>
                        
                    </div>
                </div>

                <hr>
                <h4 class="card-title">Keywords</h4>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="VideosURL" class="form-label">Palabras clave
                            <i class="fa fa-info-circle text-info"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Separa palabras por comas para relacionar esta noticia con otras de la web.">
                            </i>
                        </label>
                        <input type="text" name="Keywords" id="Keywords"
                                value="{{ old('Keywords', $publicacion->Keywords ?? '') }}"
                                class="form-control @error('Keywords') is-invalid @enderror">
                        @error('Keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror   
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </div>
    
</div>