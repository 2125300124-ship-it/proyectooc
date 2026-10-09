<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\CloudinaryImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NavigationRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(new Administrador([
            'nombres' => 'Admin',
            'apellidos' => 'Pruebas',
            'usuario' => 'admin-pruebas',
            'estado' => true,
        ]), 'admin');
    }

    private const MODULOS = [
        'administradores', 'categorias', 'clientes', 'productos', 'proveedores',
    ];

    public function test_inicio_route_loads_crud_list(): void
    {
        $this->get('/inicio')->assertOk();
    }

    public function test_each_module_renders_its_four_views(): void
    {
        $this->seedCrudRecords();

        foreach (self::MODULOS as $modulo) {
            $this->get("/{$modulo}/listar")->assertOk();
            $this->get("/{$modulo}/crear")->assertOk();
            $this->get("/{$modulo}/editar/1")->assertOk();
            $this->get("/{$modulo}/mostrar/1")->assertOk();
        }
    }

    public function test_edit_and_update_require_an_existing_id_for_each_module(): void
    {
        $this->seedCrudRecords();

        foreach (self::MODULOS as $modulo) {
            $this->get("/{$modulo}/editar/999")->assertNotFound();
            $this->get("/{$modulo}/mostrar/999")->assertNotFound();
            $this->put("/{$modulo}/actualizar/999", [])->assertNotFound();
            $this->delete("/{$modulo}/borrar/999")->assertNotFound();
        }
    }

    public function test_delete_uses_physical_or_logical_delete_based_on_relations(): void
    {
        $this->seedCrudRecords();

        $this->delete('/administradores/borrar/1')
            ->assertRedirect('/administradores/listar')
            ->assertSessionHas('exito');
        $this->assertDatabaseMissing('administradores', ['id' => 1]);

        foreach (['categorias', 'clientes', 'productos', 'proveedores'] as $tabla) {
            $this->delete("/{$tabla}/borrar/1")
                ->assertRedirect("/{$tabla}/listar")
                ->assertSessionHas('exito');
            $this->assertDatabaseHas($tabla, ['id' => 1, 'estado' => false]);
        }

        $this->assertDatabaseHas('productos', ['id' => 1, 'categoria_id' => 1, 'proveedor_id' => 1]);
    }

    public function test_each_module_can_update_its_record(): void
    {
        $this->seedCrudRecords();

        $updates = [
            'administradores' => [
                '/administradores/actualizar/1', 'administradores',
                ['nombres' => 'Admin actualizado', 'apellidos' => 'Fixture', 'correo' => 'admin-fixture@example.test', 'usuario' => 'admin-fixture', 'contrasena' => '', 'rol' => 'Ventas', 'estado' => '1'],
                ['nombres' => 'Admin actualizado'],
            ],
            'categorias' => [
                '/categorias/actualizar/1', 'categorias',
                ['nombre' => 'Categoría actualizada', 'estado' => '1'],
                ['nombre' => 'Categoría actualizada'],
            ],
            'clientes' => [
                '/clientes/actualizar/1', 'clientes',
                ['nombres' => 'Cliente actualizado', 'apellidos' => 'Fixture', 'correo' => 'cliente-fixture@example.test', 'contrasena' => '', 'direccion' => 'Dirección actualizada', 'estado' => '1'],
                ['nombres' => 'Cliente actualizado'],
            ],
            'productos' => [
                '/productos/actualizar/1', 'productos',
                ['categoria_id' => '1', 'proveedor_id' => '1', 'nombre' => 'Producto actualizado', 'descripcion' => 'Descripción actualizada', 'precio' => '150', 'existencia' => '3', 'marca' => 'Fixture', 'estado' => '1'],
                ['nombre' => 'Producto actualizado'],
            ],
            'proveedores' => [
                '/proveedores/actualizar/1', 'proveedores',
                ['nombre_empresa' => 'Proveedor actualizado', 'contacto' => 'Contacto Fixture', 'correo' => 'proveedor-fixture@example.test', 'telefono' => '5550000000', 'direccion' => 'Dirección actualizada', 'ciudad' => 'Ciudad fixture', 'estado' => '1'],
                ['nombre_empresa' => 'Proveedor actualizado'],
            ],
        ];

        foreach ($updates as [$url, $tabla, $datos, $esperado]) {
            $this->put($url, $datos)
                ->assertRedirect('/'.$tabla.'/listar')
                ->assertSessionHas('exito');
            $this->assertDatabaseHas($tabla, $esperado);
        }
    }

    public function test_update_rejects_invalid_data(): void
    {
        $this->seedCrudRecords();

        $this->from('/administradores/editar/1')->put('/administradores/actualizar/1', [
            'nombres' => 'Admin123',
            'apellidos' => 'Fixture',
            'correo' => 'admin-fixture@example.test',
            'usuario' => 'admin-fixture',
            'contrasena' => '',
            'rol' => 'Ventas',
            'estado' => '1',
        ])->assertRedirect('/administradores/editar/1')
            ->assertSessionHasErrors('nombres');

        $this->assertDatabaseHas('administradores', ['id' => 1, 'nombres' => 'Admin']);
    }

    public function test_admin_can_create_update_and_delete_a_record(): void
    {
        $data = [
            'nombres' => 'Ana',
            'apellidos' => 'García',
            'correo' => 'ana@example.test',
            'usuario' => 'ana123',
            'contrasena' => 'secret123',
            'rol' => 'Dueño',
            'estado' => '1',
        ];

        $this->post('/administradores/guardar', $data)
            ->assertRedirect('/administradores/listar')
            ->assertSessionHas('exito');
        $this->assertDatabaseHas('administradores', ['correo' => 'ana@example.test']);
        $this->assertTrue(Hash::check('secret123', (string) Administrador::first()->getAttribute('contraseña')));

        $data['nombres'] = 'Ana María';
        $this->put('/administradores/actualizar/1', $data)
            ->assertRedirect('/administradores/listar')
            ->assertSessionHas('exito');
        $this->assertDatabaseHas('administradores', ['nombres' => 'Ana María']);

        $this->delete('/administradores/borrar/1')
            ->assertRedirect('/administradores/listar')
            ->assertSessionHas('exito');
        $this->assertDatabaseMissing('administradores', ['id' => 1]);
    }

    public function test_invalid_form_data_is_rejected_without_creating_a_record(): void
    {
        $this->from('/administradores/crear')->post('/administradores/guardar', [
            'nombres' => 'Ana3',
            'apellidos' => 'García',
            'correo' => 'correo-invalido',
            'usuario' => 'ana123',
            'contrasena' => 'secret123',
            'rol' => 'Dueño',
            'estado' => '1',
        ])->assertRedirect('/administradores/crear')
            ->assertSessionHasErrors(['nombres', 'correo']);

        $this->assertDatabaseCount('administradores', 0);
    }

    public function test_numeric_fields_reject_letters(): void
    {
        $this->seedCrudRecords();

        $this->get('/productos/crear')
            ->assertOk()
            ->assertSee('data-numeric-only', false);

        $this->from('/productos/crear')->post('/productos/guardar', [
            'categoria_id' => 1,
            'proveedor_id' => 1,
            'nombre' => 'Precio inválido',
            'descripcion' => 'Prueba',
            'precio' => 'abc123',
            'existencia' => '3',
            'marca' => 'Prueba',
            'estado' => '1',
            'imagen' => UploadedFile::fake()->create('producto.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/productos/crear')
            ->assertSessionHasErrors('precio');

        $this->assertDatabaseMissing('productos', ['nombre' => 'Precio inválido']);
    }

    public function test_related_selects_load_database_options_and_product_is_saved(): void
    {
        $cloudinaryImage = 'cloudinary:1234567890:productos/test-product-image';
        $this->mockImageStorageForUploads($cloudinaryImage);
        $category = Categoria::create(['nombre' => 'Figuras de prueba', 'estado' => true]);
        $supplier = Proveedor::create([
            'nombre_empresa' => 'Proveedor de prueba',
            'contacto' => 'María Pérez',
            'correo' => 'proveedor@example.test',
            'telefono' => '5551234567',
            'direccion' => 'Calle 1',
            'ciudad' => 'Monterrey',
            'imagen' => 'proveedores/ejemplo.jpg',
            'estado' => true,
        ]);

        $this->get('/productos/crear')
            ->assertOk()
            ->assertSee('Figuras de prueba')
            ->assertSee('Proveedor de prueba');

        $this->post('/productos/guardar', [
            'categoria_id' => $category->id,
            'proveedor_id' => $supplier->id,
            'nombre' => 'Figura nueva',
            'descripcion' => 'Registro de prueba',
            'precio' => '250.00',
            'existencia' => '4',
            'marca' => 'Colección',
            'estado' => '1',
            'imagen' => UploadedFile::fake()->create('figura.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/productos/listar')->assertSessionHas('exito');

        $product = Producto::where('nombre', 'Figura nueva')->firstOrFail();
        $this->assertDatabaseHas('productos', ['nombre' => 'Figura nueva', 'categoria_id' => $category->id, 'proveedor_id' => $supplier->id]);
        $this->assertSame($cloudinaryImage, $product->imagen);
        $this->get("/productos/editar/{$product->id}")
            ->assertOk()
            ->assertSee('value="'.$category->id.'" selected', false)
            ->assertSee('value="'.$supplier->id.'" selected', false);
        $this->get("/productos/mostrar/{$product->id}")
            ->assertOk()
            ->assertSee('Figuras de prueba')
            ->assertSee('Proveedor de prueba');
        $this->get('/productos/listar')
            ->assertOk()
            ->assertSee('Figuras de prueba')
            ->assertSee('Proveedor de prueba')
            ->assertSee('https://res.cloudinary.com/test/image/upload/v1234567890/productos/test-product-image');
    }

    public function test_supplier_is_saved(): void
    {
        $this->seedCrudRecords();
        $cloudinaryImage = 'cloudinary:1234567890:proveedores/test-supplier-image';
        $this->mockImageStorageForUploads($cloudinaryImage);

        $this->post('/proveedores/guardar', [
            'nombre_empresa' => 'Nuevo proveedor',
            'contacto' => 'María López',
            'correo' => 'nuevo-proveedor@example.test',
            'telefono' => '5557654321',
            'direccion' => 'Calle Nueva 2',
            'ciudad' => 'Guadalajara',
            'estado' => '1',
            'imagen' => UploadedFile::fake()->create('proveedor.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/proveedores/listar');
        $this->assertDatabaseHas('proveedores', ['correo' => 'nuevo-proveedor@example.test']);
        $savedSupplier = Proveedor::where('correo', 'nuevo-proveedor@example.test')->firstOrFail();
        $this->assertSame($cloudinaryImage, $savedSupplier->imagen);

    }

    public function test_replacing_a_cloudinary_image_deletes_the_previous_asset(): void
    {
        $this->seedCrudRecords();
        $product = Producto::query()->firstOrFail();
        $previousImage = 'cloudinary:1234567890:productos/imagen-anterior';
        $product->update(['imagen' => $previousImage]);

        $storage = \Mockery::mock(CloudinaryImageStorage::class);
        $storage->shouldReceive('upload')
            ->once()
            ->andReturn('cloudinary:1234567891:productos/imagen-nueva');
        $storage->shouldReceive('delete')->once()->with($previousImage);
        $this->app->instance(CloudinaryImageStorage::class, $storage);

        $this->put("/productos/actualizar/{$product->id}", [
            'categoria_id' => $product->categoria_id,
            'proveedor_id' => $product->proveedor_id,
            'nombre' => $product->nombre,
            'descripcion' => $product->descripcion,
            'precio' => $product->precio,
            'existencia' => $product->existencia,
            'marca' => $product->marca,
            'estado' => '1',
            'imagen' => UploadedFile::fake()->create('nueva.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/productos/listar');

        $this->assertSame('cloudinary:1234567891:productos/imagen-nueva', $product->fresh()->imagen);
    }

    public function test_clientes_y_categorias_are_persisted(): void
    {
        $this->post('/clientes/guardar', [
            'nombres' => 'Laura',
            'apellidos' => 'Gómez',
            'correo' => 'laura@example.test',
            'contrasena' => 'secret123',
            'direccion' => 'Calle Central',
            'estado' => '1',
        ])->assertRedirect('/clientes/listar');

        $this->post('/categorias/guardar', ['nombre' => 'Acción', 'estado' => '1'])
            ->assertRedirect('/categorias/listar');
        $this->assertDatabaseHas('clientes', ['correo' => 'laura@example.test']);
        $this->assertDatabaseHas('categorias', ['nombre' => 'Acción']);
    }

    private function seedCrudRecords(): void
    {
        $category = Categoria::create(['nombre' => 'Categoría fixture', 'estado' => true]);
        $client = Cliente::create([
            'nombres' => 'Cliente', 'apellidos' => 'Fixture', 'correo' => 'cliente-fixture@example.test',
            'contraseña' => 'hashed', 'direccion' => 'Dirección fixture', 'estado' => true,
        ]);
        $supplier = Proveedor::create([
            'nombre_empresa' => 'Proveedor fixture', 'contacto' => 'Contacto Fixture',
            'correo' => 'proveedor-fixture@example.test', 'telefono' => '5550000000',
            'direccion' => 'Dirección fixture', 'ciudad' => 'Ciudad fixture',
            'imagen' => 'proveedores/fixture.jpg', 'estado' => true,
        ]);
        Administrador::create([
            'nombres' => 'Admin', 'apellidos' => 'Fixture', 'correo' => 'admin-fixture@example.test',
            'usuario' => 'admin-fixture', 'contraseña' => 'hashed', 'rol' => 'Ventas', 'estado' => true,
        ]);
        Producto::create([
            'categoria_id' => $category->id, 'proveedor_id' => $supplier->id,
            'nombre' => 'Producto fixture', 'descripcion' => 'Descripción fixture',
            'precio' => 100, 'existencia' => 2, 'marca' => 'Fixture',
            'imagen' => 'productos/fixture.jpg', 'estado' => true,
        ]);
    }

    private function mockImageStorageForUploads(string $storedReference): void
    {
        $storage = \Mockery::mock(CloudinaryImageStorage::class);
        $storage->shouldReceive('upload')->once()->andReturn($storedReference);
        $storage->shouldReceive('url')->andReturnUsing(
            fn (?string $reference): ?string => $reference !== null
                && str_starts_with($reference, 'cloudinary:')
                    ? 'https://res.cloudinary.com/test/image/upload/v'
                        . implode('/', explode(':', substr($reference, strlen('cloudinary:')), 2))
                    : ($reference ? asset('storage/'.$reference) : null)
        );

        $this->app->instance(CloudinaryImageStorage::class, $storage);
    }
}
