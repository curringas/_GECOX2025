<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Categoria;
use Illuminate\Support\Str;

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
     * Genera un slug/Url único a partir de $text. Si existe, añade sufijo -1, -2, ...
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

            $query = Categoria::where('Url', $finalUrl);

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

    protected function prepareForValidation()
    {
        $originalUrl = $this->input('Url');
        $id = $this->input('Identificador'); // null en creación, id en edición

        // Si no se pasa Url, generamos a partir de la etiqueta
        if (empty($originalUrl)) {
            $originalUrl = $this->input('Etiqueta', '');
        }

        $finalUrl = $this->comprobarSiExisteSlug($originalUrl, $id);

        $this->merge([
            'Url' => $finalUrl,
            'Etiqueta' => trim($this->input('Etiqueta', '')),
            'Menu' => $this->has('Menu') ? $this->input('Menu') : -1,
            'Padre' => ($this->input('Padre') === '---') ? null : $this->input('Padre'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->input('Identificador');

        // Normalizar campos según Tipo
        if ($this->input('Tipo') == 2) {
            $this->merge([
                'SoloEtiqueta' => 1,
                'Externo' => null,
                'Estatico' => null,
                'Url' => null,
            ]);
        } elseif ($this->input('Tipo') == 3) {
            $this->merge([
                'SoloEtiqueta' => 0,
                'Estatico' => null,
                'Url' => null,
            ]);
        } elseif ($this->input('Tipo') == 1) {
            $this->merge([
                'SoloEtiqueta' => 0,
                'Externo' => null,
            ]);
        } else {
            $this->merge([
                'SoloEtiqueta' => 0,
                'Externo' => null,
                'Estatico' => null,
                'Url' => trim($this->input('Url', ''))
            ]);
        }

        return [
            'Etiqueta' => 'required|string',
            'Url' => [
                Rule::requiredIf(function () {
                    return in_array($this->input('Tipo'), [0, 1]);
                }),
                'nullable',
                'string',
                Rule::unique('P0114_pagina', 'Url')->ignore($id, 'Identificador'),
            ],
            'Titulo' => 'required|string',
            'Padre' => 'nullable|integer',
            'Privacidad' => 'required|integer',
            'Menu' => 'required|integer',
            'Explicativo' => 'nullable|string',
            'ExplicativoProductos' => 'nullable|string',
            'SoloEtiqueta' => 'required|boolean',
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
