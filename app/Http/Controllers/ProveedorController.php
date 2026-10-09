<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\CloudinaryImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    protected string $module = 'proveedores';
    protected string $moduleTitle = 'Proveedores';
    protected string $modelClass = Proveedor::class;
    protected array $optionSources = [];
    protected array $fields = [
        ['name' => 'nombre_empresa', 'label' => 'Empresa', 'required' => true],
        ['name' => 'contacto', 'label' => 'Contacto', 'required' => true, 'rules' => ['min:2', "regex:/^[\\p{L}\\p{M}\\s'-]+$/u"]],
        ['name' => 'correo', 'label' => 'Correo', 'type' => 'email', 'required' => true, 'unique' => true],
        ['name' => 'telefono', 'label' => 'Teléfono', 'required' => true],
        ['name' => 'direccion', 'label' => 'Dirección', 'required' => true],
        ['name' => 'ciudad', 'label' => 'Ciudad', 'required' => true],
        ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => ['1' => 'Activo', '0' => 'Inactivo']],
        ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image', 'required' => true],
    ];
    protected array $examples = [
        ['id' => 1, 'nombre_empresa' => 'Figuras del Norte', 'contacto' => 'María Pérez', 'correo' => 'ventas@example.test', 'telefono' => '5551234567', 'direccion' => 'Av. Principal 10', 'ciudad' => 'Monterrey', 'estado' => '1'],
    ];

    public function listar(): View
    {
        $registros = Proveedor::query()->latest()->limit(100)->get()->toArray();

        return view('proveedores.mvc.listado', $this->datosVista(['registros' => $registros]));
    }

    public function vistaFormulario(): View
    {
        return view('proveedores.mvc.registro', $this->datosVista());
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = Validator::make($request->all(), $this->reglasValidacion())->validate();
        $registro = Proveedor::create($this->prepararDatos($datos));

        return redirect()->route('proveedores.listar')
            ->with('exito', 'Registro de Proveedores guardado correctamente.')
            ->with('registroId', $registro->getKey());
    }

    public function vistaEdicion(int $id): View
    {
        $registro = Proveedor::query()->findOrFail($id);

        return view('proveedores.mvc.edicion', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $registro = Proveedor::query()->findOrFail($id);
        $datos = Validator::make($request->all(), $this->reglasValidacion($id))->validate();
        $imagenAnterior = $registro->imagen;
        $registro->update($this->prepararDatos($datos));
        if ($registro->imagen !== $imagenAnterior) {
            app(CloudinaryImageStorage::class)->delete($imagenAnterior);
        }

        return redirect()->route('proveedores.listar')
            ->with('exito', 'Registro de Proveedores actualizado correctamente.');
    }

    public function vistaMostrar(int $id): View
    {
        $registro = Proveedor::query()->findOrFail($id);

        return view('proveedores.mvc.mostrar', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function borrar(int $id): RedirectResponse
    {
        $registro = Proveedor::query()->findOrFail($id);
        $registro->update(['estado' => false]);

        return redirect()->route('proveedores.listar')
            ->with('exito', 'Registro de Proveedores desactivado correctamente.');
    }

    private function datosVista(array $extra = []): array
    {
        return array_merge([
            'modulo' => $this->module,
            'titulo' => $this->moduleTitle,
            'modelo' => 'Proveedor',
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
                $reglaUnica = Rule::unique((new Proveedor())->getTable(), $campo['name']);
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