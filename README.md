# AI Software Factory

Base de conhecimento persistente (Projetos → Módulos → PRDs → Specs) para dar contexto contínuo a agentes de IA entre sessões, com aprovação humana obrigatória em todo conteúdo gerado.

## Estrutura

```
ai-software-factory/
├── api/          Laravel 13 + Filament 5 — fonte da verdade (banco + painel de aprovação + API REST)
└── mcp-server/   Node/TS — adaptador MCP fino, expõe a API do Laravel como tools pra agentes
```

`api/` é dona dos dados e das regras (todo PRD/Spec entra como `draft`, só vira `approved` por ação humana explícita). `mcp-server/` não tem lógica própria: só traduz chamadas de tool em requisições HTTP pra API.

## Setup

Há dois caminhos. O de Docker é o recomendado e o único testado no ambiente Linux atual.

### Opção A — Docker (recomendado)

Um container só: com SQLite não há servidor de banco para subir.

```bash
cp .env.example .env   # opcional: só se for mudar portas, caminhos ou o token
docker compose up -d
```

- Painel: `http://localhost:8000/admin` — login `admin@teamdevs.local` / `password` (configurável no `.env`).
- API: `http://localhost:8000/api` — exige `Authorization: Bearer <MCP_API_TOKEN>`.
- Banco: `api/storage/app/team-devs.sqlite`.

A porta é publicada só em `127.0.0.1`: este painel escreve arquivos no disco e roda `git` com as
suas chaves, então não pode ficar exposto na rede. Para acessar de outra máquina, use túnel SSH
(`ssh -L 8000:127.0.0.1:8000 <host>`) em vez de abrir a porta.

O container roda `composer install`, `key:generate`, `migrate` e cria o usuário do painel no boot —
tudo idempotente. `api/` é montado do host, então editar código reflete direto; só mudanças no
`docker/entrypoint.sh` exigem `docker compose build`.

**O diretório dos projetos é montado no mesmo caminho absoluto** dentro e fora do container
(`PROJECTS_DIR`, default `/home/dev/projetos`). Assim um path gravado no banco
(`/home/dev/projetos/promo-obra`) resolve igual dos dois lados, sem tradução. O `~/.ssh` entra como
somente-leitura para o painel conseguir versionar os PRDs nos repos dos projetos.

Comandos do dia a dia:

```bash
docker compose logs -f api                          # acompanhar
docker compose exec api php artisan migrate         # artisan em geral
docker compose exec api php artisan test
docker compose down                                 # parar
```

Se os arquivos criados pelo container aparecerem como `root` no host, defina `APP_UID`/`APP_GID`
no `.env` com o resultado de `id -u` / `id -g` e rode `docker compose build` (o uid também vira
usuário dentro da imagem — sem isso o `ssh` recusa rodar).

### Opção B — PHP no host (setup original, macOS)

```bash
cd api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve --port=8000
```

`MCP_API_TOKEN` no `.env` é o token que o `mcp-server` usa pra autenticar na API — mantenha os dois em sincronia.

## O registro de projetos é durável

O `.sqlite` guarda dois tipos de dado com disciplinas opostas:

- **`projects` é fonte da verdade.** É a raiz que diz *onde* os PRDs e Specs de cada projeto moram
  no disco, então não pode ser regenerado a partir dos arquivos — sem ele não se sabe onde procurar.
- **O resto é derivado.** Espelho dos markdowns, sessão e cache: descartável, reconstruível.

Como o arquivo do banco é binário e fica fora do git (`storage/app/.gitignore` é `*`), o backup é
um export versionado:

```bash
docker compose exec api php artisan projects:export   # gera api/projects.json (commitado)
docker compose exec api php artisan projects:import   # reconstrói o registro a partir dele
```

O import casa por `slug` e atualiza — nunca apaga o que não está no arquivo. E `migrate:fresh`,
`migrate:refresh`, `migrate:reset` e `db:wipe` estão **bloqueados** fora de teste
(`DB::prohibitDestructiveCommands` no `AppServiceProvider`), porque são os comandos que se digita
no automático e que apagariam o registro.

## MCP server

O MCP server fala por stdio com o cliente (Claude Code), então roda **no host**, não em container — mas aponta pra API que está no Docker.

```bash
cd mcp-server
npm install
cp .env.example .env   # preencha AISF_API_TOKEN com o mesmo valor de MCP_API_TOKEN
npm run build
```

Registrar no Claude Code (ou outro cliente MCP):

```bash
claude mcp add ai-software-factory -- node /caminho/para/team-devs/mcp-server/dist/index.js
```

Variáveis de ambiente necessárias no registro: `AISF_API_BASE_URL` (`http://127.0.0.1:8000/api`) e `AISF_API_TOKEN`.

Smoke test manual (sobe o server, lista as tools e testa o fluxo completo). Ele espera um projeto
de slug `smoke-test-project` com `id` 1 — crie pelo painel antes de rodar:

```bash
cd mcp-server
AISF_API_TOKEN=<token> node scripts/smoke-test.mjs
```

## Modelo de dados (V1)

```
projects (name, slug, description, stack, path, repo_url, branch, docs_path)
  └── modules (name, slug, description)
  └── prds (title, content, version, status, origin)
        └── specs (title, content, version, status)
```

- `status`: `draft` → `approved` → `deprecated`. Todo registro entra como `draft`, sempre — isso é forçado no banco (default) e no backend (não é possível contornar via API/MCP).
- `origin` (só em PRD): `human` ou `ai` — quem originou o conteúdo.
- `path` / `docs_path` (em Project): onde o repo do projeto mora no disco e em que subpasta ficam
  os PRDs/Specs em markdown. É o que permite ao painel ler e gravar dentro do próprio projeto.

## Tools MCP disponíveis

| Tool | Descrição |
|---|---|
| `list_projects` | Lista todos os projetos |
| `get_project_context` | Contexto completo de um projeto (módulos, PRDs, Specs) — chamar no início de qualquer sessão de trabalho |
| `search_knowledge` | Busca textual em PRDs/Specs |
| `create_prd` | Cria PRD (sempre como draft) |
| `approve_prd` | Aprova um PRD em draft |
| `create_spec` | Cria Spec vinculada a um PRD (sempre como draft) |
| `approve_spec` | Aprova uma Spec em draft |

## Roadmap

- **V1** (este repo): CRUD de Projeto/Módulo/PRD/Spec, gate de aprovação humana, MCP server local.
- **V1.1**: busca semântica (pgvector), `rules` com nível de confiança pra legado, ADRs.
- **V2**: canal de WhatsApp pra consultar status e disparar tarefas remotamente — precisa de um worker assíncrono (fila de jobs) rodando um agente de forma autônoma, já que hoje o "agente" depende de uma sessão de Claude Code ativa.
