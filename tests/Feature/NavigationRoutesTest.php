<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NavigationRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('administradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('correo')->nullable();
            $table->string('usuario')->nullable();
            $table->string('contraseña')->nullable();
            $table->string('imagen')->nullable();
            $table->string('rol')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function test_inicio_route_loads(): void
    {
        $this->get('/inicio')->assertOk();
    }

    public function test_clientes_y_administradores_routes_load(): void
    {
        $this->get('/clientes')->assertOk();
        $this->get('/administradores')->assertOk();
    }

    public function test_admin_can_register_and_persist(): void
    {
        $response = $this->post('/admin/guardar', [
            'nombres' => 'Ana',
            'apellidos' => 'García',
            'correo' => 'ana@correo.com',
            'usuario' => 'ana123',
            'contrasena' => 'secret123',
            'rol' => 'Dueño',
            'estado' => true,
        ]);

        $response->assertRedirect('/administradores');
        $this->assertDatabaseHas('administradores', [
            'correo' => 'ana@correo.com',
            'usuario' => 'ana123',
            'rol' => 'Dueño',
        ]);
    }
}
