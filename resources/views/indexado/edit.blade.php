@extends('layouts.master')

@section('title')
    @lang('titulos.Editar_Usuario')
@endsection

@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}">
@endsection

@section('content')    
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            @lang('titulos.Editar_Usuario')
        @endslot
    @endcomponent

    <form action="{{ route('indexado.update') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
                <h4>Datos para SEO</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="Nombre" class="form-label">Nombre del sitio (Title)</label>
                    <input type="text" class="form-control" id="Nombre" name="Nombre" value="{{ old('Nombre', $indexado->Nombre) }}">
                </div>

                <div class="mb-3">
                    <label for="Descripcion" class="form-label">Descripción (Meta Description)</label>
                    <textarea class="form-control" id="Descripcion" name="Descripcion" rows="4">{{ old('Descripcion', $indexado->Descripcion) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="Keywords" class="form-label">Keywords (Meta Keywords)</label>
                    <textarea class="form-control" id="Keywords" name="Keywords" rows="3">{{ old('Keywords', $indexado->Keywords) }}</textarea>
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
                    <input type="url" class="form-control" id="Facebook" name="Facebook" value="{{ old('Facebook', $indexado->Facebook) }}">
                </div>

                <div class="mb-3">
                    <label for="Twitter" class="form-label">Twitter URL</label>
                    <input type="url" class="form-control" id="Twitter" name="Twitter" value="{{ old('Twitter', $indexado->Twitter) }}">
                </div>

                <div class="mb-3">
                    <label for="Google" class="form-label">Google+ URL (obsoleto, opcional)</label>
                    <input type="url" class="form-control" id="Google" name="Google" value="{{ old('Google', $indexado->Google) }}">
                </div>

                <div class="mb-3">
                    <label for="Youtube" class="form-label">Youtube URL</label>
                    <input type="url" class="form-control" id="Youtube" name="Youtube" value="{{ old('Youtube', $indexado->Youtube) }}">
                </div>

                <div class="mb-3">
                    <label for="Instagram" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control" id="Instagram" name="Instagram" value="{{ old('Instagram', $indexado->Instagram) }}">
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
                        <input type="number" class="form-control" id="ContadorFacebook" name="ContadorFacebook" value="{{ old('ContadorFacebook', $indexado->ContadorFacebook) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorTwitter" class="form-label">Contador Twitter</label>
                        <input type="number" class="form-control" id="ContadorTwitter" name="ContadorTwitter" value="{{ old('ContadorTwitter', $indexado->ContadorTwitter) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorInstagram" class="form-label">Contador Instagram</label>
                        <input type="number" class="form-control" id="ContadorInstagram" name="ContadorInstagram" value="{{ old('ContadorInstagram', $indexado->ContadorInstagram) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="ContadorTelegram" class="form-label">Contador Telegram</label>
                        <input type="number" class="form-control" id="ContadorTelegram" name="ContadorTelegram" value="{{ old('ContadorTelegram', $indexado->ContadorTelegram) }}">
                    </div>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary my-4">Guardar Cambios</button>
    </form>
</div>

@endsection

@section('script')
    <!-- bootstrap datepicker -->
    <script src="{{ URL::asset('build/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Idioma español del deatepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/locales/bootstrap-datepicker.es.min.js"></script>
    
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/xpt_categorias.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/pages/xpt_seo.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/palabrea_publicaciones.init.js') }}"></script>

@endsection

@section('script-bottom')

@endsection