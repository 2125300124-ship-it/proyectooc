<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Services\CloudinaryImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    protected string $module = 'categorias';
    protected string $moduleTitle = 'Categorías';
    protected string $modelClass = Categoria::class;
    protected array $optionSources = [];
    protected array $fields = [
        ['name' => 'nombre', 'label' => 'Nombre', 'required' => true, 'unique' => true],
        ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'required' => true, 'options' => ['1' => 'Activo', '0' => 'Inactivo']],
        ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'image'],
    ];
    protected array $examples = [['id' => 1, 'nombre' => 'Figuras', 'estado' => '1']];

    public function listar(): View
    {
        $registros = Categoria::query()->latest()->limit(100)->get()->toArray();

        return view('categorias.mvc.listado', $this->datosVista(['registros' => $registros]));
    }

    public function vistaFormulario(): View
    {
        return view('categorias.mvc.registro', $this->datosVista());
    }

    public function registrar(Request $request): RedirectResponse
    {
        $datos = Validator::make($request->all(), $this->reglasValidacion())->validate();
        $registro = Categoria::create($this->prepararDatos($datos));

        return redirect()->route('categorias.listar')
            ->with('exito', 'Registro de Categorías guardado correctamente.')
            ->with('registroId', $registro->getKey());
    }

    public function vistaEdicion(int $id): View
    {
        $registro = Categoria::query()->findOrFail($id);

        return view('categorias.mvc.edicion', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $registro = Categoria::query()->findOrFail($id);
        $datos = Validator::make($request->all(), $this->reglasValidacion($id))->validate();
        $imagenAnterior = $registro->imagen;
        $registro->update($this->prepararDatos($datos));
        if ($registro->imagen !== $imagenAnterior) {
            app(CloudinaryImageStorage::class)->delete($imagenAnterior);
        }

        return redirect()->route('categorias.listar')
            ->with('exito', 'Registro de Categorías actualizado correctamente.');
    }

    public function vistaMostrar(int $id): View
    {
        $registro = Categoria::query()->findOrFail($id);

        return view('categorias.mvc.mostrar', $this->datosVista([
            'id' => $id,
            'registro' => $registro->toArray(),
        ]));
    }

    public function borrar(int $id): RedirectResponse
    {
        $registro = Categoria::query()->findOrFail($id);
        $registro->update(['estado' => false]);

        return redirect()->route('categorias.listar')
            ->with('exito', 'Registro de Categorías desactivado correctamente.');
    }

    private function datosVista(array $extra = []): array
    {
        return array_merge([
            'modulo' => $this->module,
            'titulo' => $this->moduleTitle,
            'modelo' => 'Categoria',
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
                $reglaUnica = Rule::unique((new Categoria())->getTable(), $campo['name']);
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