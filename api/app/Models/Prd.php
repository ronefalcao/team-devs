<?php

namespace App\Models;

use App\Enums\ArtifactStatusEnum;
use App\Enums\PrdOriginEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prd extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'title',
        'content',
        'version',
        'status',
        'origin',
    ];

    protected $casts = [
        'status' => ArtifactStatusEnum::class,
        'origin' => PrdOriginEnum::class,
        'version' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(Spec::class);
    }
}
