<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'proveedor_id', 'nombre', 'descripcion', 'precio', 'existencia', 'marca', 'imagen', 'estado',
    ];

    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'estado' => 'boolean'];
    }

    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function ventas() { return $this->hasMany(Venta::class); }
}