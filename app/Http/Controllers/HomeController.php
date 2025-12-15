<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Portada;
use App\Models\PortadaIzquierda;
use App\Models\PortadaCentral;
use App\Models\PortadaDerecha;
use App\Models\PortadaSlider;
use App\Models\Publicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return abort(404);
    }

    public function root()
    {
        $portada = Portada::first();
        $sliders = PortadaSlider::orderBy('Orden')->get();
        $derechos = PortadaDerecha::with(['publicacion','publicacion.imagenes'])->orderBy('Orden')->get();
        $centrales = PortadaCentral::with(['publicacion','publicacion.imagenes'])->orderBy('Orden')->get();
        $izquierdos = PortadaIzquierda::with(['publicacion','publicacion.imagenes'])->orderBy('Orden')->get();
        return view('index', compact('portada','sliders','derechos','centrales','izquierdos'));
    }

    static function obtenterPortada($tabla)
    {
        $modelClass = null;
        switch ($tabla){
            case 'portada_slider':
                    $modelClass = PortadaSlider::class ;
                break;
            case 'portada_derecha':
                    $modelClass = PortadaDerecha::class ;
                break;
            case 'portada_central':
                    $modelClass = PortadaCentral::class ;
                break;
            case 'portada_izquierda':
                    $modelClass = PortadaIzquierda::class ;
                break;
            case 'portada':
                    $modelClass = Portada::class ;
                break;
        }
        return $modelClass;
    }

    public function ajaxBuscarPublicaciones(Request $request)
    {
        // Obtener el término de búsqueda de Select2
        $searchTerm = $request->get('term'); 

        $publicaciones = Publicacion::select('Identificador', 'Titulo')
            // Buscar por Título (o el campo que desees)
            ->where('Titulo', 'LIKE', '%' . $searchTerm . '%') 
            ->limit(10) // Limitar resultados para rendimiento
            ->get();

        $resultados = $publicaciones->map(function ($publicacion) {
            return [
                'id' => $publicacion->Identificador,
                'text' => $publicacion->Titulo,
            ];
        });

        // Select2 espera los resultados bajo la clave 'results'
        return response()->json(['results' => $resultados]);
    }

    public function ajaxDatosNoticia(Request $request)
    {

        if (!$request->has('tabla') || !$request->has('id')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        //obtenemos el modelo izquieda centro o derecha
        $modelo = $this->obtenterPortada($request->tabla);
        $portada=$modelo::find($request->id);

        return response()->json([
            'publicacion' => $portada->Publicacion ?? null,
            'titulo' => $portada->publicacion->Titulo
        ]);
    }

    public function ajaxGuardarNoticia(Request $request)
    {

        if (!$request->has('noticiaTabla') || !$request->has('noticiaPublicacion')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        //obtenemos el modelo izquieda centro o derecha
        $modelo = $this->obtenterPortada($request->noticiaTabla);
        if ($request->has('noticiaIdentificador') && $request->noticiaIdentificador!="nuevo"){
            $portada=$modelo::find($request->noticiaIdentificador);
        }else{
            $portada=new $modelo;
        }
        //dd($request->noticiaPublicacion);
        $portada->Publicacion=$request->noticiaPublicacion;
        $portada->save();
        return response()->json(['success' => true, 'message' => 'Banner actualizado successfully.']);
        
    }
    

    public function ajaxDatosBanner(Request $request)
    {

        if (!$request->has('tabla') || !$request->has('banner')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        //dd($request->all());
        $campo_titulo = $request->banner.'Titulo';
        $campo_imagen = $request->banner.'Imagen';
        $campo_url = $request->banner.'Url';
        $campo_destino = $request->banner.'Destino';
        $campo_codigo = $request->banner.'CodigoFuente';

        // Lógica para manejar diferentes tablas si es necesario
        $modelo = $this->obtenterPortada($request->tabla);
        if ($request->has('orden')) {
            $portada=$modelo::find($request->id);
        } else {
            $portada=$modelo::first();
        }

        return response()->json([
            'titulo' => $portada->$campo_titulo ?? '',
            'imagen' => $portada->$campo_imagen ?? '',
            'url' => $portada->$campo_url ?? '',
            'destino' => $portada->$campo_destino ?? '',
            'codigo' => $portada->$campo_codigo ?? '',
            'orden' => $request->orden ?? '',
        ]);
    }

    public function ajaxGuardarBanner(Request $request)
    {

        if (!$request->has('bannerTabla') || !$request->has('bannerBanner')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        $campo_titulo = $request->bannerBanner.'Titulo';
        $campo_imagen = $request->bannerBanner.'Imagen';
        $campo_imagenMovil = $request->bannerBanner.'ImagenMovil';
        $campo_url = $request->bannerBanner.'Url';
        $campo_destino = $request->bannerBanner.'Destino';
        $campo_codigo = $request->bannerBanner.'CodigoFuente';
        $campo_orden = "Orden";

        $modelo = $this->obtenterPortada($request->bannerTabla);
        if ($request->has('bannerIdentificador') && $request->bannerIdentificador!="nuevo"){
            $portada = $modelo::find($request->bannerIdentificador);
        }else{
            $portada=new $modelo;
        }

        //Guardo la imagen si la hay
        $imageFile = $request->file('bannerImagen');
        $removeImage = $request->input('removeBanner') === '1';
        if ($imageFile || $removeImage) {
            // Primero eliminamos la imagen que hubiera
            if ($portada->$campo_imagen!=null) {
                Storage::disk('public')->delete('banners/' . $portada->$campo_imagen);

                $portada->$campo_imagen=null; 
            }

            if ($imageFile){
                // Luego guardamos la nueva imagen
                $imageName = $campo_imagen . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('banners', $imageFile, $imageName);
                $request->bannerImagen = 'banners/' . $imageName;
                $portada->$campo_imagen = $request->bannerImagen;
            }
        } 
        //Imagen para movil
        $imageFileMovil = $request->file('bannerImagenMovil');
        $removeImage = $request->input('removeBanner') === '1';
        if ($imageFileMovil || $removeImage) {
            // Primero eliminamos la imagen que hubiera
            if ($portada->$campo_imagenMovil) {
                Storage::disk('public')->delete('banners/' . $portada->$campo_imagenMovil);

                $portada->$campo_imagenMovil=null; 
            }

            if ($imageFileMovil){
                // Luego guardamos la nueva imagen
                $imageName = $campo_imagenMovil . '_' . time() . '.' . $imageFileMovil->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('banners', $imageFileMovil, $imageName);
                $request->bannerImagenMovil = 'banners/' . $imageName;
                $portada->$campo_imagenMovil = $request->bannerImagenMovil;
            }
        } 
        $portada->$campo_titulo = $request->bannerTitulo;
        $portada->$campo_url = $request->bannerUrl;
        $portada->$campo_destino = $request->bannerDestino;
        $portada->$campo_codigo = $request->bannerCodigo;
        $portada->save();

        return response()->json(['success' => true, 'message' => 'Banner actualizado successfully.']);
    }

    //Eliminamos cualquier item de portada
    public function ajaxEliminar(Request $request)
    {
        if (!$request->has('tabla') || !$request->has('eliminar')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        $modelo = $this->obtenterPortada($request->tabla);
        if ($request->has('id')){
            $portada = $modelo::find($request->id);
        }else{
            $portada=new $modelo;
        }

        if ($request->eliminar=="Noticia"){
            if ($request->has('id')){
                //Sera uno de las tablas acumulativas de items (izda, central, slider o derecha)
                $portada->delete();
            }else{
                //Sera de la tabla principal y solo hay que poner a null, no se elimina el registro que siempre hay uno
                $portada->Principal=null;
                $portada->save();
            }
            return response()->json(['success' => true, 'message' => 'Noticia eliminada successfully.']);

        }else{

            $campo_titulo = $request->eliminar.'Titulo';
            $campo_imagen = $request->eliminar.'Imagen';
            $campo_imagenMovil = $request->eliminar.'ImagenMovil';
            $campo_url = $request->eliminar.'Url';
            $campo_destino = $request->eliminar.'Destino';
            $campo_codigo = $request->eliminar.'CodigoFuente';
            $campo_orden = "Orden";

            //dd($campo_imagen,$portada->$campo_imagen,$portada);
            // Eliminar imagen del disco si existe
            if ($portada->$campo_imagen) {
                Storage::disk('public')->delete($portada->$campo_imagen);
            }
            if ($portada->$campo_imagenMovil) {
                Storage::disk('public')->delete($portada->$campo_imagenMovil);
            }

           if ($request->has('id')){
                // Si es una tabla con orden, eliminamos el registro completo
                $portada->delete();
            } else {
                //Si es la tabla portada principal solo ponemos a null los campos
                $portada->$campo_titulo = null;
                $portada->$campo_imagen = null;
                $portada->$campo_imagenMovil = null;
                $portada->$campo_url = null;
                $portada->$campo_destino = null;
                $portada->$campo_codigo = null;
                $portada->save();

            }
            return response()->json(['success' => true, 'message' => 'Banner eliminado successfully.']);
        }
    }

    public function ajaxReordenar(Request $request)
    {
        if (!$request->has('tabla') || !$request->has('orden_ids')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        switch ($request->tabla){
            case 'portada_slider':
                $modelClass = PortadaSlider::class;
                break;
            case 'portada_derecha':
                $modelClass = PortadaDerecha::class;
                break;
            case 'portada_central':
                $modelClass = PortadaCentral::class;
                break;
            case 'portada_izquierda':
                $modelClass = PortadaIzquierda::class;
                break;
        }
        $ordenIds = $request->orden_ids; // Array de IDs en el nuevo orden
        try {
            DB::beginTransaction(); //Esto comienza una transacción para asegurar la integridad de los datos y todas las sentencias se ejecutan de una vez en el commit de abajo
            
            // Recorrer los IDs y actualizar el campo 'Orden' para que sea correlativo
            foreach ($ordenIds as $orden => $id) {
                // La posición del ID en el array (+1) es el nuevo valor de 'Orden' (1, 2, 3, ...)
                $nuevoOrden = $orden + 1; 

                $modelClass::where('Identificador', $id)
                        ->update(['Orden' => $nuevoOrden]);
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'Message' => 'El orden de los banners ha sido actualizado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden de los banners: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => "User Details Updated successfully!"
            ], 200); // Status code here
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => true,
                'Message' => "Something went wrong!"
            ], 200); // Status code here
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }
}
