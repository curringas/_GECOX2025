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
        $derechos = PortadaDerecha::orderBy('Orden')->get();
        $centrales = PortadaCentral::orderBy('Orden')->get();
        $izquierdos = PortadaIzquierda::orderBy('Orden')->get();
        return view('index', compact('portada','sliders','derechos','centrales','izquierdos'));
    }


    public function ajaxDatosBanners(Request $request)
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
        switch ($request->tabla){
            case 'portada_slider':
                if ($request->has('orden')) {
                    $portada = PortadaSlider::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaSlider::first();
                }
                break;
            case 'portada_derecha':
                if ($request->has('orden')) {
                    $portada = PortadaDerecha::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaDerecha::first();
                }
                break;
            case 'portada_central':
                if ($request->has('orden')) {
                    $portada = PortadaCentral::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaCentral::first();
                }
                break;
            case 'portada_izquierda':
                if ($request->has('orden')) {
                    $portada = PortadaIzquierda::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaIzquierda::first();
                }
                break;
            default:
                $portada = Portada::first();
        }

        return response()->json([
            'titulo' => $portada->$campo_titulo ?? '',
            'imagen' => $portada->$campo_imagen ?? '',
            'url' => $portada->$campo_url ?? '',
            'destino' => $portada->$campo_destino ?? '',
            'codigo' => $portada->$campo_codigo ?? '',
            'orden' => $request->orden ?? '',
        ]);
        return response()->json([]);
    }

    public function ajaxGuardarBanner(Request $request)
    {

        if (!$request->has('bannerTabla') || !$request->has('bannerBanner')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        $campo_titulo = $request->bannerBanner.'Titulo';
        $campo_imagen = $request->bannerBanner.'Imagen';
        $campo_url = $request->bannerBanner.'Url';
        $campo_destino = $request->bannerBanner.'Destino';
        $campo_codigo = $request->bannerBanner.'CodigoFuente';
        $campo_orden = "Orden";

        switch ($request->bannerTabla){
            case 'portada_slider':
                if ($request->has('bannerOrden') && $request->bannerOrden == 'nuevo') {
                    $portada = new PortadaSlider();
                    $maxOrden = PortadaSlider::max('Orden');
                    $portada->$campo_orden = $maxOrden ? $maxOrden + 1 : 1;
                } else {
                    $portada = PortadaSlider::where('Orden', $request->bannerOrden)->first();
                }
                break;
            case 'portada_derecha':
                if ($request->has('bannerOrden') && $request->bannerOrden == 'nuevo') {
                    $portada = new PortadaDerecha();
                    $maxOrden = PortadaDerecha::max('Orden');
                    $portada->$campo_orden = $maxOrden ? $maxOrden + 1 : 1;
                } else {
                    $portada = PortadaDerecha::where('Orden', $request->bannerOrden)->first();
                }
                break;
            case 'portada_central':
                if ($request->has('bannerOrden') && $request->bannerOrden == 'nuevo') {
                    $portada = new PortadaCentral();
                    $maxOrden = PortadaCentral::max('Orden');
                    $portada->$campo_orden = $maxOrden ? $maxOrden + 1 : 1;
                } else {
                    $portada = PortadaCentral::where('Orden', $request->bannerOrden)->first();
                }
                break;
            case 'portada_izquierda':
                if ($request->has('bannerOrden') && $request->bannerOrden == 'nuevo') {
                    $portada = new PortadaIzquierda();
                    $maxOrden = PortadaIzquierda::max('Orden');
                    $portada->$campo_orden = $maxOrden ? $maxOrden + 1 : 1;
                } else {
                    $portada = PortadaIzquierda::where('Orden', $request->bannerOrden)->first();
                }
                $portada = PortadaIzquierda::first();
                break;
            default:
                $portada = Portada::first();
        }

        //Guardo la imagen si la hay
        $imageFile = $request->file('bannerImagen');
        $removeImage = $request->input('removeBanner') === '1';
        if ($imageFile || $removeImage) {
            // Primero eliminamos la imagen que hubiera
            if ($portada->$campo_imagen) {
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
        $portada->$campo_titulo = $request->bannerTitulo;
        $portada->$campo_url = $request->bannerUrl;
        $portada->$campo_destino = $request->bannerDestino;
        $portada->$campo_codigo = $request->bannerCodigo;
        $portada->save();

        return response()->json(['success' => true, 'message' => 'Banner actualizado successfully.']);
    }

    public function ajaxEliminarBanner(Request $request)
    {
        if (!$request->has('tabla') || !$request->has('banner')) {
            return response()->json(['success' => false, 'message' => 'Parámetros insuficientes.']);
        }

        $campo_titulo = $request->banner.'Titulo';
        $campo_imagen = $request->banner.'Imagen';
        $campo_url = $request->banner.'Url';
        $campo_destino = $request->banner.'Destino';
        $campo_codigo = $request->banner.'CodigoFuente';
        $campo_orden = "Orden";

        switch ($request->tabla){
            case 'portada_slider':
                if ($request->has('orden')) {
                    $portada = PortadaSlider::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaSlider::first();
                }
                break;
            case 'portada_derecha':
                if ($request->has('orden')) {
                    $portada = PortadaDerecha::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaDerecha::first();
                }
                break;
            case 'portada_central':
                if ($request->has('orden')) {
                    $portada = PortadaCentral::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaCentral::first();
                }
                break;
            case 'portada_izquierda':
                if ($request->has('orden')) {
                    $portada = PortadaIzquierda::where('Orden', $request->orden)->first();
                } else {
                    $portada = PortadaIzquierda::first();
                }
                break;
            default:
                $portada = Portada::first();
        }

        // Eliminar imagen del disco si existe
        if ($portada->$campo_imagen) {
            Storage::disk('public')->delete('banners/' . $portada->$campo_imagen);
        }
        
        if ($request->has('orden')) {
            // Si es una tabla con orden, eliminamos el registro completo
            $portada->delete();
            return response()->json(['success' => true, 'message' => 'Banner eliminado successfully.']);
        } else {
            // Limpiar los campos en la base de datos
            $portada->$campo_titulo = null;
            $portada->$campo_imagen = null;
            $portada->$campo_url = null;
            $portada->$campo_destino = null;
            $portada->$campo_codigo = null;
            $portada->save();

            return response()->json(['success' => true, 'message' => 'Banner eliminado successfully.']);
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
