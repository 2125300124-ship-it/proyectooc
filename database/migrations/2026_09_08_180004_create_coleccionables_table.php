<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coleccionables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Relaciones con las 3 categorías solicitadas
            $table->foreignId('categoria_id')->constrained('categorias')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('universo_id')->constrained('universos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('condicion_id')->constrained('condiciones')->onUpdate('cascade')->onDelete('restrict');

            $table->decimal('precio', 10, 2);
            $table->integer('existencia')->default(0);
            $table->decimal('descuento', 5, 2)->default(0);

            // 3 Imágenes solicitadas
            $table->string('imagen1')->nullable();
            $table->string('imagen2')->nullable();
            $table->string('imagen3')->nullable();

            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coleccionables');
    }
};
