<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coleccionable extends Model
{
    use HasFactory;

    protected $table = 'coleccionables';
    protected $fillable = [
        'nombre', 'descripcion', 'categoria_id', 'universo_id', 'condicion_id',
        'precio', 'existencia', 'descuento', 'imagen1', 'imagen2', 'imagen3', 'estado'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function universo(): BelongsTo
    {
        return $this->belongsTo(Universo::class, 'universo_id');
    }

    public function condicion(): BelongsTo
    {
        return $this->belongsTo(Condicion::class, 'condicion_id');
    }
}
