<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * O "local" do projeto: onde o repo mora no disco. É o que permite ao painel
     * ler e gravar os PRDs/Specs em markdown dentro do próprio projeto, em vez
     * de guardar o conteúdo aqui.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('path')->nullable()->after('stack');
            $table->string('repo_url')->nullable()->after('path');
            $table->string('branch')->default('main')->after('repo_url');
            // Onde ficam os markdowns, relativo à raiz do projeto.
            $table->string('docs_path')->default('docs')->after('branch');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['path', 'repo_url', 'branch', 'docs_path']);
        });
    }
};
