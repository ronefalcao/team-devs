<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Cria o usuário de acesso ao painel do Filament.
 *
 * O painel exige login, então sem isto não há como entrar no ambiente local.
 * É idempotente: rodar de novo não duplica nem sobrescreve a senha existente.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');

        if (blank($email)) {
            $this->command?->warn('ADMIN_EMAIL não definido — nenhum usuário criado.');

            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Usuário {$email} já existe.");

            return;
        }

        User::create([
            'name' => env('ADMIN_NAME', 'Admin'),
            'email' => $email,
            'password' => env('ADMIN_PASSWORD', 'password'),
        ]);

        $this->command?->info("Usuário do painel criado: {$email}");
    }
}
