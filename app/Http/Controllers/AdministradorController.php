<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdministradorController extends Controller
{
    public function listar()
    {
        $admins = Administrador::latest()->paginate(20);

        return view('administradores.listado', compact('admins'));
    }

    public function vistaFormulario()
    {
        return view('administradores.registro');
    }

    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'email', 'unique:administradores,correo'],
            'usuario' => ['required', 'string', 'max:50', 'unique:administradores,usuario'],
            'contrasena' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'string', 'max:50'],
            'estado' => ['nullable', 'boolean'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin = new Administrador();
        $admin->nombres = $request->nombres;
        $admin->apellidos = $request->apellidos;
        $admin->correo = $request->correo;
        $admin->usuario = $request->usuario;
        $admin->contraseña = Hash::make($request->contrasena);
        $admin->rol = $request->rol;
        $admin->estado = $request->has('estado') ? (bool) $request->estado : true;

        if ($request->hasFile('imagen')) {
            $admin->imagen = $request->file('imagen')->store('administradores', 'public');
        }

        $admin->save();

        return redirect()->route('administradores.listado')->with('exito', 'Administrador registrado correctamente');
    }

    public function vistaEdicion()
    {
        return view('administradores.edicion');
    }

    public function actualizar()
    {
        return 'Aqui va el update';
    }

    public function vistaMostrar()
    {
        return view('administradores.mostrar');
    }

    public function borrar()
    {
        return 'Aqui va el delete';
    }
}
