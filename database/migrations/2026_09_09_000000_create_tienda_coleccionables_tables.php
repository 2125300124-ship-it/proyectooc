<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('telefono');
            $table->string('direccion');
            $table->string('contrasena');
                $table->string('imagen');
            $table->date('fecha_registro');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion');
            $table->string('tipo');
            $table->unsignedTinyInteger('edad_recomendada');
            $table->string('imagen');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa');
            $table->string('contacto');
            $table->string('correo')->unique();
            $table->string('telefono');
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('imagen');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('existencia')->default(0);
            $table->string('marca');
            $table->string('imagen');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_venta');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago');
            $table->string('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('clientes');
    }
};