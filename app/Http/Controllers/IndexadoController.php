<?php

namespace App\Http\Controllers;

use App\Models\Indexado;
use Illuminate\Http\Request;

class IndexadoController extends Controller
{
    /**
     * Show the form for editing the site's SEO and social media information.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Since there is only one row, we fetch the first one.
        $indexado = Indexado::first();

        // If for some reason the row doesn't exist, we create a new empty model
        // so the view doesn't fail.
        if (!$indexado) {
            $indexado = new Indexado();
        }

        return view('indexado.edit', compact('indexado'));
    }

    /**
     * Update the site's SEO and social media information in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'Nombre' => 'required|string|max:100',
            'Descripcion' => 'required|string',
            'Keywords' => 'required|string',
            'Facebook' => 'required|url|max:255',
            'Twitter' => 'required|url|max:255',
            'Google' => 'nullable|string|max:255',
            'Youtube' => 'required|url|max:255',
            'Instagram' => 'required|url|max:255',
            'ContadorFacebook' => 'required|integer',
            'ContadorTwitter' => 'required|integer',
            'ContadorInstagram' => 'required|integer',
            'ContadorTelegram' => 'required|integer',
        ]);

        // Try to find the first record, and update it.
        // If it does not exist, create it.
        $indexado = Indexado::first();
        if ($indexado) {
            $indexado->update($data);
        } else {
            // Since 'Nombre' is the primary key, it must be in the data.
            Indexado::create($data);
        }

        return redirect()->route('indexado.edit')
                         ->with('success', 'Datos de indexado y redes sociales actualizados correctamente.');
    }
}
