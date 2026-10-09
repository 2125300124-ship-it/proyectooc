<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\CloudinaryImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClienteController extends Controller
{
    protected string $module = 'clientes';
    protected string $moduleTitle = 'Clientes';
    protected string $modelClass = Cliente::class;
    protected array $optionSources = [];
    protected array $fields = [
        ['name' => 'nombres', 'label' => 'Nombres', 'required' => true, 'rules' => ['min:2', 'max:100', "regex:/^[\\p{L}\\p{M}\\s'-]+$/u"]],
        ['name' => 'apellidos', 'label' => 'Apellidos', 'required' => true, 'rules' => ['min:2', 'max:100', "regex:/^[\\p{L}\\p{M}\\s'-]+$/u"]],
        ['name' => 'correo', 'label' => 'Correo', 'type' => 'email', 'required' => true, 'unique' => true],
        ['name' => 'contrasena', 'column' => 'contraseña', 'label' => 'Contraseña', 'type' => 'password', 'required' => true, 'rules' => ['min:8']],
        ['name' => 'direccion', 'label' => 'Dirección', 'type' => 'textarea'],
        ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => ['1' => 'Activo', '0' => 'Inactivo']],
        ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image'],
    ];
    protected array $examples = [
        ['id' => 1, 'nombres' => 'Laura', 'apellidos' => 'Gómez', 'correo' => 'laura@example.test', 'direccion' => 'Dirección de ejemplo', 'estado' => '1'],
    ];

    public function listar(): View
    {
        $registros = Cliente::query()->latest()->limit(100)->get()->toArray();

        return view('clientes.mvc.listado', $this->datosVista(['registros' => $registros]));
    }

    public function vistaFormulario(): View
    {
        return view('clientes.mvc.registro', $this->datosVista());
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = Validator::make($request->all(), $this->reglasValidacion())->validate();
        $registro = Cliente::create($this->prepararDatos($datos));

        return redirect()->route('clientes.listar')
            ->with('exito', 'Registro de Clientes guardado correctamente.')
            ->with('registroId', $registro->getKey());
    }

    public function vistaEdicion(int $id): View
    {
        $registro = Cliente::query()->findOrFail($id);

        return view('clientes.mvc.edicion', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $registro = Cliente::query()->findOrFail($id);
        $datos = Validator::make($request->all(), $this->reglasValidacion($id))->validate();
        $imagenAnterior = $registro->imagen;
        $registro->update($this->prepararDatos($datos));
        if ($registro->imagen !== $imagenAnterior) {
            app(CloudinaryImageStorage::class)->delete($imagenAnterior);
        }

        return redirect()->route('clientes.listar')
            ->with('exito', 'Registro de Clientes actualizado correctamente.');
    }

    public function vistaMostrar(int $id): View
    {
        $registro = Cliente::query()->findOrFail($id);

        return view('clientes.mvc.mostrar', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function borrar(int $id): RedirectResponse
    {
        $registro = Cliente::query()->findOrFail($id);
        $registro->update(['estado' => false]);

        return redirect()->route('clientes.listar')
            ->with('exito', 'Registro de Clientes desactivado correctamente.');
    }

    private function datosVista(array $extra = []): array
    {
        return array_merge([
            'modulo' => $this->module,
            'titulo' => $this->moduleTitle,
            'modelo' => 'Cliente',
            'campos' => $this->fields,
            'registro' => [],
        ], $extra);
    }

    private function reglasValidacion(?int $id = null): array
    {
        $reglas = [];

        foreach ($this->fields as $campo) {
            $tipo = $campo['type'] ?? 'text';
            $requerido = ($campo['required'] ?? false)
                && !($id !== null && in_array($tipo, ['password', 'image'], true));
            $reglasCampo = [$requerido ? 'required' : 'nullable'];

            if ($tipo === 'email') {
                $reglasCampo[] = 'email';
            } elseif ($tipo === 'image') {
                $reglasCampo = array_merge($reglasCampo, ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']);
            } elseif ($tipo === 'select') {
                $reglasCampo[] = Rule::in(array_keys($campo['options']));
            }

            if ($campo['unique'] ?? false) {
                $reglaUnica = Rule::unique((new Cliente())->getTable(), $campo['name']);
                $reglasCampo[] = $id === null ? $reglaUnica : $reglaUnica->ignore($id);
            }

            $reglas[$campo['name']] = array_merge($reglasCampo, $campo['rules'] ?? []);
        }

        return $reglas;
    }

    private function prepararDatos(array $datos): array
    {
        foreach ($this->fields as $campo) {
            $nombre = $campo['name'];

            if (($campo['type'] ?? '') === 'image') {
                if (!isset($datos[$nombre])) {
                    unset($datos[$nombre]);
                    continue;
                }

                $datos[$nombre] = app(CloudinaryImageStorage::class)->upload($datos[$nombre], $this->module);
            }

            if (isset($campo['column']) && array_key_exists($nombre, $datos)) {
                if (($campo['type'] ?? '') === 'password') {
                    if (blank($datos[$nombre])) {
                        unset($datos[$nombre]);
                        continue;
                    }

                    $datos[$nombre] = Hash::make($datos[$nombre]);
                }

                $datos[$campo['column']] = $datos[$nombre];
                unset($datos[$nombre]);
            }
        }

        return $datos;
    }
}