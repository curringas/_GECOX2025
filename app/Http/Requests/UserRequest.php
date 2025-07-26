<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        if ($this->has('dob')) {

            // Mapa de meses en español a inglés
            $months = [
                'Ene' => 'Jan',
                'Abr' => 'Apr',
                'Ago' => 'Aug',
                'Sep' => 'Sep',
                'Dic' => 'Dec'
            ];


            // Obtener el valor de la fecha
            $dob = $this->input('dob');
            

            // Reemplazar los meses en español por sus equivalentes en inglés
            $dob = strtr($dob, $months);
            $this->merge([
            'dob' => Carbon::createFromFormat('d M, Y', $dob)->format('Y-m-d')
            ]);
        }
    }


    public function rules(): array
    {    
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'dob' => 'nullable|date_format:Y-m-d',
            'avatar' => 'nullable|image|mimes:jpeg,png,gif,jpg|max:2048',
            'newpassword' => 'nullable|string',
            'newpassword_confirmation' => 'nullable|string|same:newpassword',
        ];
    }
}
