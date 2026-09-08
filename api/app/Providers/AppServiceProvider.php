<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A tabela `projects` é dado durável: ela é a raiz que diz ONDE os PRDs e
        // Specs de cada projeto moram no disco, então não pode ser regenerada a
        // partir dos arquivos. Bloqueia migrate:fresh/refresh/wipe e db:wipe, que
        // são comandos que se digita no automático em projeto de dev.
        // Use `php artisan projects:export` antes de qualquer coisa arriscada.
        DB::prohibitDestructiveCommands($this->app->isProduction() || ! $this->app->runningUnitTests());
    }
}
