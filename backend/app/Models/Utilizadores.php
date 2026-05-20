<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Utilizadores extends Model
{
 protected $fillable = ['name', 'username'];

    protected $table = 'utilizadores';

    public function posts()
    {
        return $this->hasMany(Posts::class, 'utilizador_id');
    }
    

}
