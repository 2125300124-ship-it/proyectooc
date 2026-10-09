<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cliente extends Authenticatable
{
    use HasFactory;

    protected $table = 'clientes';
    protected $fillable = ['nombres', 'apellidos', 'correo', 'google_id', 'contraseña', 'direccion', 'imagen', 'estado'];
    protected $hidden = ['contraseña'];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function getAuthPassword(): string
    {
        return $this->contraseña;
    }
}
