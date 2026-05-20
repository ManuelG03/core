<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posts extends Model
{
    protected $fillable = ['content', 'utilizador_id'];

    protected $table = 'posts';

     public function utilizador()
    {
        return $this->belongsTo(Utilizadores::class, 'utilizador_id');
    }

    
}
