<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'nombre_empresa', 'contacto', 'correo', 'telefono', 'direccion', 'ciudad', 'imagen', 'estado',
    ];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function productos() { return $this->hasMany(Producto::class); }
}