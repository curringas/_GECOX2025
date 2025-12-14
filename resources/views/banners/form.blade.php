@csrf
<input type="hidden" name="Identificador" value="{{ $banner->Identificador ?? '' }}">

<div class="row">
    <div class="col-md-8">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Datos banner</h4>

            <div class="mb-3">
                <label class="form-label">Título <span class="text-danger">*</span></label>
                <input name="Titulo" value="{{ old('Titulo', $banner->Titulo ?? '') }}" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen (Banner)</label>
                <input type="file" name="Banner" class="form-control mt-2" />
                <input type="hidden" name="old_Banner" value="{{ $banner->Banner ?? '' }}" />
                <input type="hidden" name="remove_banner" id="remove_banner" value="0" />
            </div>

            <div class="mb-3">
                <label class="form-label">ImagenMovil (Banner para versión móvil)</label>
                <input type="file" name="BannerMovil" class="form-control mt-2" />
                <input type="hidden" name="old_BannerMovil" value="{{ $banner->BannerMovil ?? '' }}" />
            </div>

            <div class="mb-3">
                <label class="form-label">URL</label>
                <input name="URL" value="{{ old('URL', $banner->URL ?? '') }}" class="form-control" />
            </div>

            <div class="mb-3">
                <label class="form-label">Código (HTML)</label>
                <textarea name="Codigo" rows="10" class="form-control">{{ old('Codigo', $banner->Codigo ?? '') }}</textarea>
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
            <p>{{ isset($banner) ? 'Ultima modificación el ' . \Carbon\Carbon::parse($banner->Fecha)->format('d/m/Y H:i') : '' }}
                <br>
                {{ isset($banner) ? 'Creado por ' . $banner->Creador : '' }}
            </p>

            <div class="mb-3">
                <label class="form-label">Tipo / Zona</label>
                <select name="Tipo" class="form-select" required>
                    <option value="">Seleciona Tipo/Zona</option>
                    <option value="0" {{ old('Tipo', $banner->Tipo ?? '') == 1 ? 'selected' : '' }}>{{ config('gecox_banners.tipos.0') }}</option>
                    <option value="1" {{ old('Tipo', $banner->Tipo ?? '') == 1 ? 'selected' : '' }}>{{ config('gecox_banners.tipos.1') }}</option>
                    <option value="2" {{ old('Tipo', $banner->Tipo ?? '') == 2 ? 'selected' : '' }}>{{ config('gecox_banners.tipos.2') }}</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Target</label>
                <select name="Target" class="form-select mb-2">
                    <option value="1" {{ old('Target', $banner->Target ?? '') == 1 ? 'selected' : '' }}>{{ config('gecox_banners.targets.1') }}</option>
                    <option value="0" {{ old('Target', $banner->Target ?? '') == 0 ? 'selected' : '' }}>{{ config('gecox_banners.targets.0') }}</option>
                </select>
            </div>

            <hr>
            <h5>Categorías</h5>
            @php
                $selectedIds = old('categorias', isset($banner) ? $banner->categorias->pluck('Identificador')->toArray() : []);
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

            @if (isset($banner))
                <div class="mb-3 mt-3" id="current-banner-preview">
                    <div class="position-relative">
                        @if ($banner->Codigo)
                            {!! $banner->Codigo !!}
                        @else
                            <a href="{{ $banner->URL ? $banner->URL : '#' }}" target="_blank">
                                <img src="{{ asset('storage/banners/' . $banner->Banner) }}" alt="{{ $banner->Titulo }}" class="img-fluid" />
                            </a>
                        @endif
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="delete-current-banner" title="Eliminar imagen">&times;</button>
                    </div>
                    <small id="remove-banner-note" class="text-muted ms-2" style="display:none;">Imagen marcada para eliminación</small>
                </div>
            @endif
        </div>
    </div>
    </div>
</div>

@push('scripts')
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
@endpush