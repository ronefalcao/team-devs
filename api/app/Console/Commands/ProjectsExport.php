<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

/**
 * Backup legível e versionável do registro de projetos.
 *
 * O `.sqlite` é o operacional, mas é um binário fora do git. Este export é a
 * cópia que vai commitada: se o banco sumir, `projects:import` reconstrói.
 */
class ProjectsExport extends Command
{
    protected $signature = 'projects:export {--path= : Arquivo de destino}';

    protected $description = 'Exporta o registro de projetos para JSON versionável';

    public function handle(): int
    {
        $path = $this->option('path') ?: base_path('projects.json');

        $projects = Project::query()
            ->orderBy('slug')
            ->get(['name', 'slug', 'description', 'stack', 'path', 'repo_url', 'branch', 'docs_path'])
            ->toArray();

        file_put_contents(
            $path,
            json_encode($projects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
        );

        $this->info(count($projects).' projeto(s) exportado(s) para '.realpath($path));

        return self::SUCCESS;
    }
}
