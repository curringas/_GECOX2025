<?php

namespace App\Http\Requests;

use App\Models\Publicacion;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class PublicacionRequest extends FormRequest
{
    /**
     * Determine if the categoria is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    private function comprobarSiExisteSlug(string $text, $id = null): string
    {
        $base = Str::slug(trim($text ?: ''), '-');
        if ($base === '') {
            $base = 'categoria';
        }

        $suffix = '';
        $count = 1;

        do {
            $finalUrl = $base . $suffix;

            $query = Publicacion::where('Url', $finalUrl);

            if ($id) {
                $query->where('Identificador', '!=', $id);
            }

            $exists = $query->exists();

            if ($exists) {
                $suffix = '-' . $count++;
            }
        } while ($exists);

        return $finalUrl;
    }
     
    public function prepareForValidation()
    {
        //dd('preparing validation in PublicacionRequest',$this->all());
        //preparando el SLUG o url , no puede haber 2 iguales
        $originalUrl = $this->input('Url');
        $id = $this->input('Identificador'); // null en creación, id en edición

        // Si no se pasa Url, generamos a partir de la etiqueta
        if (empty($originalUrl)) {
            $originalUrl = $this->input('Etiqueta', '');
        }

        $finalUrl = $this->comprobarSiExisteSlug($originalUrl, $id);

        $this->merge([
            'Activa' => $this->has('Activa') ? 1 : 0,
            'Video' => $this->has('Video') ? trim($this->Video) : '',
            'GaleriaURL' => $this->has('GaleriaURL') ? trim($this->GaleriaURL) : '',
            'Keywords' => $this->has('Keywords') ? trim($this->Keywords) : '',
            'AutorEmail' => $this->has('AutorEmail') ? trim($this->AutorEmail) : '',
            'Url' => $finalUrl,
            'LlevaComentarios' => $this->has('LlevaComentarios') ? 1 : 0,
            'Email' => $this->has('Email') ? trim($this->Email) : '',
            'Lugar' => $this->has('Lugar') ? trim($this->Lugar) : '',
            'LugarTipo' => $this->has('Lugar') ? trim($this->LugarTipo) : '',
            'Logotipo' => $this->has('Logotipo') ? trim($this->Logotipo) : '',
            'Introduccion' => $this->has('Introduccion') ? trim($this->Introduccion) : '',
            'Autor' => $this->has('Autor') ? trim($this->Autor) : '',
            'AutorTwitter' => $this->has('AutorTwitter') ? trim($this->AutorTwitter) : '',
            'Creador' => auth()->user()->email ?? 'system',
        ]);
    }


    public function rules(): array
    {    
        $id = $this->input('Identificador');

        return [
            'Activa' => 'required|boolean',
            'Titulo' => 'required|string',
            'Url' => 'required|string',
            'Introduccion' => 'nullable|string',
            'Contenido' => 'nullable|string',
            'TextoApoyo' => 'nullable|string',
            'Subtitulo' => 'nullable|string',
            'Fecha' => 'required|date',
            'FechaInicio' => 'nullable|date',
            'FechaFin' => 'nullable|date|after_or_equal:FechaInicio',
            'FechaSalida' => 'nullable|date',
            'Autor' => 'nullable|string',
            'Lugar' => 'nullable|string',
            'Activacion' => 'nullable|date',
            'Desactivacion' => 'nullable|date|after_or_equal:Activacion',
            'Notas' => 'nullable|string',
            'Logotipo' => 'nullable|string',
            'LugarTipo' => 'nullable|string',
            'Video' => 'nullable|string',
            'LlevaComentarios' => 'nullable|boolean',
            'GaleriaURL' => 'nullable|string',
            'Keywords' => 'nullable|string',
            'Privacidad' => 'required|integer',
            'Visitas' => 'nullable|integer',
            'AutorTwitter' => 'nullable|string',
            'AutorEmail' => 'nullable|email',
            'MetaTitle' => 'nullable|string',
            'MetaDescription' => 'nullable|string',
            'Creador' => 'required|string',
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['image', 'mimes:' . implode(',', config('gecox_imagenes.tipos')), 'max:' . config('gecox_imagenes.peso.0')],
            'documentos' => ['nullable', 'array'],
            'documentos.*' => ['file', 'mimes:' . implode(',', config('gecox_documentos.tipos')), 'max:' . config('gecox_documentos.peso.0')],
        ];
    }
}
