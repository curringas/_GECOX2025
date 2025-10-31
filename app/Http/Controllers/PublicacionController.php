<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;

class PublicacionController extends Controller
{
    public function index()
    {
        return Publicacion::all();
    }

    public function show($id)
    {
        return Publicacion::findOrFail($id);
    }

    public function store(Request $request)
    {
        $publicacion = Publicacion::create($request->all());
        return response()->json($publicacion, 201);
    }

    public function update(Request $request, $id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->update($request->all());
        return response()->json($publicacion, 200);
    }

    public function destroy($id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->delete();
        return response()->json(null, 204);
    }
}
