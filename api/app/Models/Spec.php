<?php

namespace App\Models;

use App\Enums\ArtifactStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spec extends Model
{
    use HasFactory;

    protected $fillable = [
        'prd_id',
        'title',
        'content',
        'version',
        'status',
    ];

    protected $casts = [
        'status' => ArtifactStatusEnum::class,
        'version' => 'integer',
    ];

    public function prd(): BelongsTo
    {
        return $this->belongsTo(Prd::class);
    }
}
