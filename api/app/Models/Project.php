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
        'path',
        'repo_url',
        'branch',
        'docs_path',
    ];

    /**
     * Caminho absoluto da pasta de docs (PRDs/Specs) deste projeto no disco.
     * O container monta os projetos no mesmo caminho do host, então o valor
     * gravado aqui vale dos dois lados.
     */
    public function docsAbsolutePath(): ?string
    {
        if (blank($this->path)) {
            return null;
        }

        return rtrim($this->path, '/').'/'.trim($this->docs_path ?: 'docs', '/');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function prds(): HasMany
    {
        return $this->hasMany(Prd::class);
    }
}
