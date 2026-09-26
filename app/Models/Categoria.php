<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $fillable = ['nombre', 'imagen', 'estado'];

    public function coleccionables(): HasMany
    {
        return $this->hasMany(Coleccionable::class, 'categoria_id');
    }
}
