<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Laravel ja assumeix 'users' i 'id', així que no cal especificar-ho
    // Si vols mantenir-ho explícitament:
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relació 1:N amb llistes creades
    /**
     * Relació 1:N amb les llistes de compra creades per l'usuari.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function llistesCreades(): HasMany
    {
        return $this->hasMany(LlistaCompra::class, 'user_id');
    }
    public function etiquetas(): HasMany
{
    return $this->hasMany(Etiqueta::class, 'user_id');
}


    // Relació N:M amb llistes compartides
    /**
     * Relació N:M amb les llistes de compra compartides amb l'usuari.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function llistesCompartides(): BelongsToMany
    {
        return $this->belongsToMany(
            LlistaCompra::class,
            'usuaris_llistes_compra',
            'user_id',
            'id_llista_compra',
            'id',
            'id_llista_compra'
        );
    }
}