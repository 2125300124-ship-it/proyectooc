<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalles_pedidos';
    protected $fillable = ['pedido_id', 'coleccionable_id', 'cantidad', 'precio', 'descuento'];
}
