<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
 
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, '_sport_id');
    }

   
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, '_sport_id');
    }
}
