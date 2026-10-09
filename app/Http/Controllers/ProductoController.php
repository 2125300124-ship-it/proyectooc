<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Services\CloudinaryImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    protected string $module = 'productos';
    protected string $moduleTitle = 'Productos';
    protected string $modelClass = Producto::class;
    protected array $fields = [
        ['name' => 'categoria_id', 'label' => 'Categoría', 'type' => 'select', 'required' => true],
        ['name' => 'proveedor_id', 'label' => 'Proveedor', 'type' => 'select', 'required' => true],
        ['name' => 'nombre', 'label' => 'Nombre', 'required' => true],
        ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea', 'required' => true],
        ['name' => 'precio', 'label' => 'Precio', 'type' => 'number', 'required' => true, 'rules' => ['min:0']],
        ['name' => 'existencia', 'label' => 'Existencia', 'type' => 'number', 'required' => true, 'rules' => ['integer', 'min:0']],
        ['name' => 'marca', 'label' => 'Marca', 'required' => true],
        ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => ['1' => 'Activo', '0' => 'Inactivo']],
        ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image', 'required' => true],
    ];
    protected array $optionSources = [
        'categoria_id' => [Categoria::class, 'nombre'],
        'proveedor_id' => [Proveedor::class, 'nombre_empresa'],
    ];

    public function listar(): View
    {
        $registros = Producto::query()->latest()->limit(100)->get()->toArray();

        return view('productos.mvc.listado', $this->datosVista(['registros' => $registros]));
    }

    public function vistaFormulario(): View
    {
        return view('productos.mvc.registro', $this->datosVista());
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = Validator::make($request->all(), $this->reglasValidacion())->validate();
        $registro = Producto::create($this->prepararDatos($datos));

        return redirect()->route('productos.listar')
            ->with('exito', 'Registro de Productos guardado correctamente.')
            ->with('registroId', $registro->getKey());
    }

    public function vistaEdicion(int $id): View
    {
        $registro = Producto::query()->findOrFail($id);

        return view('productos.mvc.edicion', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $registro = Producto::query()->findOrFail($id);
        $datos = Validator::make($request->all(), $this->reglasValidacion($id))->validate();
        $imagenAnterior = $registro->imagen;
        $registro->update($this->prepararDatos($datos));
        if ($registro->imagen !== $imagenAnterior) {
            app(CloudinaryImageStorage::class)->delete($imagenAnterior);
        }

        return redirect()->route('productos.listar')
            ->with('exito', 'Registro de Productos actualizado correctamente.');
    }

    public function vistaMostrar(int $id): View
    {
        $registro = Producto::query()->findOrFail($id);

        return view('productos.mvc.mostrar', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function borrar(int $id): RedirectResponse
    {
        $registro = Producto::query()->findOrFail($id);
        $registro->update(['estado' => false]);

        return redirect()->route('productos.listar')
            ->with('exito', 'Registro de Productos desactivado correctamente.');
    }

    private function datosVista(array $extra = []): array
    {
        $campos = $this->fields;

        foreach ($campos as &$campo) {
            $campo['options'] = $this->opcionesCampo($campo['name'], $campo['options'] ?? []);
        }
        unset($campo);

        return array_merge([
            'modulo' => $this->module,
            'titulo' => $this->moduleTitle,
            'modelo' => 'Producto',
            'campos' => $campos,
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
            } elseif ($tipo === 'number') {
                $reglasCampo[] = 'numeric';
            } elseif ($tipo === 'date') {
                $reglasCampo[] = 'date';
            } elseif ($tipo === 'image') {
                $reglasCampo = array_merge($reglasCampo, ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']);
            } elseif ($tipo === 'select') {
                $reglasCampo[] = Rule::in(array_keys($this->opcionesCampo($campo['name'], $campo['options'] ?? [])));
            }

            if ($campo['unique'] ?? false) {
                $reglaUnica = Rule::unique((new Producto())->getTable(), $campo['name']);
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

    private function opcionesCampo(string $nombre, array $opciones = []): array
    {
        if (!isset($this->optionSources[$nombre])) {
            return $opciones;
        }

        [$modelo, $columna] = $this->optionSources[$nombre];

        return $modelo::query()->orderBy($columna)->pluck($columna, 'id')->all();
    }
}