<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CategoriaRequest extends FormRequest
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
        $this->merge([
            'Etiqueta' => trim($this->Etiqueta),
            'Menu' => $this->has('Menu') ? $this->Menu : -1,
            'Padre' => $this->Padre=="---" ? null : $this->Padre,
        ]);
    }


    public function rules(): array
    {    
        $id = $this->input('Identificador');

        // 0 Con publicaciones
        // 1 Enlace a página estática
        // 2 Etiqueta
        // 3 Enlace externo

        if ($this->Tipo == 2) {
            // Etiqueta
            $this->merge([
                'SoloEtiqueta' => 1,
                'Externo' => null,
                'Estatico' => null,
                'Url' => null,
            ]);
        } elseif ($this->Tipo == 3) {
            // 3 Enlace externo
            $this->merge([
                'SoloEtiqueta' => 0,
                'Estatico' => null,
                'Url' => null,
            ]);
        }elseif ($this->Tipo == 1) {
            // 1 Enlace a página estática
            $this->merge([
                'SoloEtiqueta' => 0,
                'Externo' => null,
            ]);
        } else {
            // Con publicaciones
            $this->merge([
                'SoloEtiqueta' => 0,
                'Externo' => null,
                'Estatico' => null,
                'Url' => trim($this->Url)
            ]);
        }
        return [
            'Etiqueta' => 'required|string',
            'Url' => 'nullable|string|unique:P0114_pagina,Url',
            'Titulo' => 'required|string',
            'Padre' => 'nullable|integer',
            'Privacidad' => 'required|integer',
            'Menu' => 'required|integer',
            'Explicativo' => 'nullable|string',
            'ExplicativoProductos' => 'nullable|string',
            'SoloEtiqueta' => 'required|boolean',
            'Url' => 'required_if:Tipo,0,1|unique:P0114_pagina,Url,' . $id . ',Identificador',

            'Externo' => 'required_if:Tipo,3',
            'Estatico' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($this->input('Tipo') == 1 && empty($value)) {
                        $fail('El campo Enlace externo es obligatorio');
                    }
                },
            ],
            'MetaTitle' => 'nullable|string',
            'MetaDescription' => 'nullable|string',
        ];
    }
}
