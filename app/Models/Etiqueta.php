<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';
    protected $primaryKey = 'id_etiqueta';
    protected $fillable = ['etiqueta_producte', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

