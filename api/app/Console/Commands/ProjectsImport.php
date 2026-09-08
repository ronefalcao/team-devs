<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

/**
 * Reconstrói o registro de projetos a partir do export.
 *
 * Casa por `slug` e atualiza — nunca apaga o que não está no arquivo, pra que
 * importar um export defasado não destrua projetos cadastrados depois dele.
 */
class ProjectsImport extends Command
{
    protected $signature = 'projects:import {--path= : Arquivo de origem}';

    protected $description = 'Importa o registro de projetos de um JSON exportado';

    public function handle(): int
    {
        $path = $this->option('path') ?: base_path('projects.json');

        if (! is_file($path)) {
            $this->error("Arquivo não encontrado: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);

        if (! is_array($rows)) {
            $this->error('JSON inválido.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            if (blank($row['slug'] ?? null)) {
                $this->warn('Registro sem slug ignorado.');

                continue;
            }

            $project = Project::firstOrNew(['slug' => $row['slug']]);
            $project->exists ? $updated++ : $created++;
            $project->fill($row)->save();
        }

        $this->info("{$created} criado(s), {$updated} atualizado(s).");

        return self::SUCCESS;
    }
}
