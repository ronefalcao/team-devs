<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'stack',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function prds(): HasMany
    {
        return $this->hasMany(Prd::class);
    }
}
