<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id', 'producto_id', 'fecha_venta', 'subtotal', 'impuesto', 'total',
           'metodo_pago', 'imagen', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_venta' => 'datetime',
            'subtotal' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'total' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }

    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}