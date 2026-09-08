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

### 1. Banco de dados

Usa o Postgres compartilhado da infra local (`infra_postgres`, container em `~/Documents/codigos/infra`, porta `5433`) — o mesmo Postgres que já é usado por outros projetos (ex: `contratacoes_laravel_api`, banco `g2saude`), só que com um banco próprio (`ai_software_factory`) dentro da mesma instância. **Não é infra exclusiva deste projeto** — não pare/suba a stack inteira (`docker compose up/down` sem argumento) por causa deste projeto, isso afeta MySQL/Redis/Adminer usados por outras coisas. Se só o Postgres não estiver rodando: `docker compose -f ~/Documents/codigos/infra/docker-compose.yml up -d postgres`.

Localmente, o único processo que este projeto precisa rodar de fato é o `php artisan serve` (passo 2) — o Postgres é gerenciado como infra compartilhada, fora do ciclo de vida deste projeto.

O banco `ai_software_factory` já foi criado nele.

### 2. API (Laravel + Filament)

```bash
cd api
cp .env.example .env   # se ainda não existir
composer install
php artisan key:generate
php artisan migrate
php artisan serve --port=8000
```

Painel em `http://127.0.0.1:8000/admin`.

`MCP_API_TOKEN` no `.env` é o token que o `mcp-server` usa pra autenticar na API — mantenha os dois em sincronia.

### 3. MCP server

```bash
cd mcp-server
npm install
cp .env.example .env   # preencha AISF_API_TOKEN com o mesmo valor de MCP_API_TOKEN do api/.env
npm run build
```

Registrar no Claude Code (ou outro cliente MCP):

```bash
claude mcp add ai-software-factory -- node /Users/rone/Documents/codigos/ai-software-factory/mcp-server/dist/index.js
```

Variáveis de ambiente necessárias no registro: `AISF_API_BASE_URL` e `AISF_API_TOKEN`.

Smoke test manual (sobe o server, lista as tools e testa o fluxo completo):

```bash
cd mcp-server
AISF_API_TOKEN=<token> node scripts/smoke-test.mjs
```

## Modelo de dados (V1)

```
projects (name, slug, description, stack)
  └── modules (name, slug, description)
  └── prds (title, content, version, status, origin)
        └── specs (title, content, version, status)
```

- `status`: `draft` → `approved` → `deprecated`. Todo registro entra como `draft`, sempre — isso é forçado no banco (default) e no backend (não é possível contornar via API/MCP).
- `origin` (só em PRD): `human` ou `ai` — quem originou o conteúdo.

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
