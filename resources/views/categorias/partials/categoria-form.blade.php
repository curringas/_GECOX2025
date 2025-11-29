<form method="POST" id="formCategoria" action="{{  route('categorias.store') }}"  role="form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="Identificador" value="{{ isset($amodificar) ? $amodificar->Identificador : '' }}">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="etiqueta" class="form-label">Etiqueta* <span class="text-muted">(Nombre en el menú)</span></label>
            <input type="text" name="Etiqueta" class="form-control bg-warning-subtle" value="{{ old('Etiqueta', $amodificar->Etiqueta ?? '') }}" id="Etiqueta" placeholder="Etiqueta" required>
            
        </div>
        <div class="col-md-6 mb-3">
            <label for="visible" class="form-label">Mostrar en el menú</label>
            <div class="form-check form-switch form-switch-lg mb-0" dir="ltr">
                <input class="form-check-input" type="checkbox" id="active-switch" name="Menu" value="0" {{ old('Menu', $amodificar->Menu ?? 0) == 0 ? 'checked="checked"' : '' }}>
                <label class="form-check-label ms-2 mb-0" for="active-switch">Visible</label>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo</label>
        @php
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
        @endphp
        <select class="form-select" name="Tipo" id="tipo">
            <option value="0" {{ $tipoSeleccionado == 0 ? 'selected' : '' }}>Con publicaciones</option>
            <option value="1" {{ $tipoSeleccionado == 1 ? 'selected' : '' }}>Estática (Sólo soporte técnico)</option>
            <option value="2" {{ $tipoSeleccionado == 2 ? 'selected' : '' }}>Etiqueta (Contenedor de submenús)</option>
            <option value="3" {{ $tipoSeleccionado == 3 ? 'selected' : '' }}>Enlace a web externa</option>
        </select>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="etiqueta" class="form-label">Pertenece a</label>
            <select class="form-select" name="Padre" id="padre" required="">
                <option value="---" {{ old('Padre', $amodificar->Padre ?? null) == null ? 'selected'  : '' }}>
                    Inicio
                </option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->Identificador }}" 
                                        {{ old('Padre', $amodificar->Padre ?? '') == $categoria->Identificador  ? 'selected'  : '' }}>
                        {{ $categoria->Etiqueta }}
                    </option>
                    @if ($categoria->hijos && $categoria->hijos->count())
                        @foreach ($categoria->hijos as $hijo)
                        <option value="{{ $hijo->Identificador }}"
                                        {{ old('Padre', $amodificar->Padre ?? '') == $hijo->Identificador  ? 'selected' : '' }}>
                                &nbsp;> {{ $hijo->Etiqueta }}
                            </option>
                            @if ($hijo->hijos && $hijo->hijos->count())
                                @foreach ($hijo->hijos as $nieto)
                                    <option value="{{ $nieto->Identificador }}" 
                                        {{ old('Padre', $amodificar->Padre ?? '') == $nieto->Identificador  ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>> {{ $nieto->Etiqueta }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3 hideable-solo-etiqueta">
            <label for="orden" class="form-label">Privacidad</label>
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
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="titulo" class="form-label">Título <span class="text-muted">(Titulo h1 de la página obligatorio para publicaciones o estática)</span></label>
        <input type="text" name="Titulo" class="form-control " value="{{ old('Titulo', $amodificar->Titulo ?? '') }}" id="titulo" placeholder="Titulo">
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="explicativo" class="form-label">Descripción superior</label>
        <textarea id="explicativo" class="form-control" rows="3" name="Explicativo" placeholder="Introduce una descripción corta para la parte superior de la sección">
            {{ old('Explicativo', $amodificar->Explicativo ?? '') }}
        </textarea>
    </div>
    <div class="mb-3 hideable-solo-etiqueta">
        <label for="explicativoproductos" class="form-label">Descripción inferior</label>
        <textarea id="explicativoproductos" class="form-control" rows="3" name="ExplicativoProductos" placeholder="Introduce una descripción larga para la parte inferior de la sección">
            {{ old('ExplicativoProductos', $amodificar->ExplicativoProductos ?? '') }}
        </textarea>
    </div>
    
    
    <div class="mb-3 hideable-link-external" style="display: none;">
        <label for="externo" class="form-label">Enlace externo</label>
        <input type="text" name="Externo" class="form-control " value="{{ old('Externo', $amodificar->Externo ?? '') }}" id="externo" placeholder="https://">
        @error('Externo')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3 hideable-link-external" style="display: none;">
        <label for="target" class="form-label">Abrir en</label>
        <select class="form-select" name="Target" id="target" required="">
            <option value="0" {{ old('Target', $amodificar->Target ?? 0) == 0 ? 'selected' : '' }}>
                En la misma ventana
            </option>
            <option value="1" {{ old('Target', $amodificar->Target ?? 0) == 1 ? 'selected' : '' }}>
                En una nueva ventana
            </option>
        </select>
    </div>
    <div class="mb-3 hideable-estatico" style="display: none;">
        <label for="estatico" class="form-label">Archivo estático</label>
        <input type="text" name="Estatico" class="form-control " value="{{ old('Estatico', $amodificar->Estatico ?? '') }}" id="estatico" placeholder="archivo.html">
    </div>
    
    <div class="p-3 mb-3 background-seo hideable-solo-etiqueta">
        <h4 class="font-size-16 mb-3">Opciones posicionamiento SEO</h4>
        <div class="mb-3">
            <label for="Url" class="form-label">Slug <span class="text-muted">(url de la noticia,parecida al titulo. Usa - en lugar de espacios)</span></label>
            <input type="text" id="Url" class="form-control" value="{{ old('Url', $amodificar->Url ?? '') }}" placeholder="Url de la noticia" name="Url">
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-muted">(60 carateres aprox.)</span></label>
            <input type="text" id="MetaTitle" class="form-control" maxlength="70" value=" {{ $amodificar->MetaTitle ?? '' }}" placeholder="Introduce un titulo similar diferente" name="MetaTitle">
            <small class="text-muted position-absolute end-0 me-4" id="titleCount">
                0 / 70
            </small>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Description <span class="text-muted">(160 carateres aprox.)</span></label>
            <textarea id="MetaDescription"  class="form-control" maxlength="200" rows="3" name="MetaDescription" placeholder="Introduce una descripción para motores de búsqueda">{{ $amodificar->MetaDescription ?? '' }}</textarea>
            <small class="text-muted position-absolute end-0 me-4" id="descCount">
                0 / 170
            </small>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
</form>

@section('script-bottom')

    <script src="{{ URL::asset('build/js/pages/xpt_seo.init.js') }}"></script>

<script>

    
$(document).ready(function() {

    
    // SEO - Usamos selectores de ID: #Etiqueta y #Url para el SLUG de la publicación
    //console.log("Iniciando setupSlugGenerator para categoría..." + $('#Etiqueta').val() + $('#Url').val());
    setupSlugGenerator('#Etiqueta', '#Url');

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
@endsection