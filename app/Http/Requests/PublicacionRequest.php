<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
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

     
    public function prepareForValidation()
    {
       /* $this->merge([
            'Etiqueta' => trim($this->Etiqueta),
            'Menu' => $this->has('Menu') ? $this->Menu : -1,
            'Padre' => $this->Padre=="---" ? null : $this->Padre,
        ]);*/
        $this->merge([
            'Activa' => $this->has('Activa') ? 1 : 0,
            'Video' => $this->has('Video') ? trim($this->Video) : '',
            'GaleriaURL' => $this->has('GaleriaURL') ? trim($this->Video) : '',
            'Keywords' => $this->has('Keywords') ? trim($this->Keywords) : '',
            'AutorEmail' => $this->has('AutorEmail') ? trim($this->AutorEmail) : '',
            'Url' => trim($this->Url),
            'LlevaComentarios' => $this->has('LlevaComentarios') ? 1 : 0,
            'Email' => $this->has('Email') ? trim($this->Email) : '',
            'Lugar' => $this->has('Lugar') ? trim($this->Lugar) : '',
            'LugarTipo' => $this->has('Lugar') ? trim($this->LugarTipo) : '',
            'Logotipo' => $this->has('Logotipo') ? trim($this->Logotipo) : '',
            'Introduccion' => $this->has('Introduccion') ? trim($this->Introduccion) : '',
            'Autor' => $this->has('Autor') ? trim($this->Autor) : '',
            'AutorTwitter' => $this->has('AutorTwitter') ? trim($this->Autor) : '',
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
            'documentos' => ['nullable', 'array'],
            // 2 MB = 2048 KB. La regla 'max' de archivos usa kilobytes (KB).
            'documentos.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip', 'max:2048'],
        ];
    }
}
