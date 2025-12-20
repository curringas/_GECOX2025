<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function prepareForValidation()
    {
       /* $this->merge([
            'Etiqueta' => trim($this->Etiqueta),
            'Menu' => $this->has('Menu') ? $this->Menu : -1,
            'Padre' => $this->Padre=="---" ? null : $this->Padre,
        ]);*/
        $this->merge([
            'Creador' => auth()->user()->email ?? 'system',
            'Fecha' => now(),
        ]);
    }

    public function rules(): array
    {
        // Obtener configuración de forma segura y consistente
        $allowedMimes = implode(',', config('gecox_imagenes.tipos', ['jpeg', 'png', 'gif']));
        // Laravel espera el tamaño en KB para la regla 'max'
        $maxSizeKB = config('gecox_banners.pesos.grande', 800); 

        return [
            'Identificador' => 'nullable|integer',
            // La validación ahora apunta al campo 'Banner' que es el file input
            'Banner'        => 'nullable|file|image|mimes:'.$allowedMimes.'|max:'.$maxSizeKB, 
            'Titulo'        => 'nullable|string|max:200',
            'URL'           => 'nullable|url|max:255',
            'Tipo'          => 'required|integer',
            'Creador'       => 'nullable|string|max:100',
            'Fecha'         => 'nullable|date',
            'Target'        => 'nullable|integer',
            'Codigo'        => 'nullable|string',
        ];
    }
}