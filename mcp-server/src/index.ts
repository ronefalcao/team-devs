import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";
import { api } from "./api-client.js";

const server = new McpServer({
  name: "ai-software-factory",
  version: "1.0.0",
});

function json(data: unknown) {
  return { content: [{ type: "text" as const, text: JSON.stringify(data, null, 2) }] };
}

server.tool(
  "list_projects",
  "Lista todos os projetos cadastrados na AI Software Factory (nome, slug, stack, descrição).",
  {},
  async () => json(await api.listProjects()),
);

server.tool(
  "get_project_context",
  "Recupera o contexto atual de um projeto: dados do projeto, módulos e todos os PRDs (com suas Specs). " +
    "Use isto no início de qualquer trabalho num projeto, em vez de assumir o histórico da conversa.",
  { project_slug: z.string().describe("Slug do projeto, ex: 'educacao-api'") },
  async ({ project_slug }) => json(await api.getProjectContext(project_slug)),
);

server.tool(
  "search_knowledge",
  "Busca textual por PRDs e Specs cujo título ou conteúdo combinem com a query. " +
    "Opcionalmente filtra por projeto.",
  {
    query: z.string().min(2).describe("Termo de busca"),
    project_slug: z.string().optional().describe("Slug do projeto para restringir a busca"),
  },
  async ({ query, project_slug }) => json(await api.searchKnowledge(query, project_slug)),
);

server.tool(
  "create_prd",
  "Cria um novo PRD. Sempre entra com status 'draft' — precisa ser aprovado por um humano " +
    "(via approve_prd ou pelo painel) antes de ser considerado válido.",
  {
    project_id: z.number().int().describe("ID do projeto"),
    module_id: z.number().int().optional().describe("ID do módulo, se aplicável"),
    title: z.string().describe("Título do PRD"),
    content: z.string().describe("Conteúdo do PRD em markdown"),
    origin: z.enum(["human", "ai"]).describe("Quem originou o conteúdo: 'human' ou 'ai'"),
  },
  async (input) => json(await api.createPrd(input)),
);

server.tool(
  "approve_prd",
  "Aprova um PRD que está em draft, marcando-o como fonte de verdade vigente.",
  { prd_id: z.number().int() },
  async ({ prd_id }) => json(await api.approvePrd(prd_id)),
);

server.tool(
  "create_spec",
  "Cria uma nova Spec vinculada a um PRD. Sempre entra com status 'draft' — precisa ser aprovada " +
    "por um humano antes de ser considerada válida.",
  {
    prd_id: z.number().int().describe("ID do PRD ao qual esta Spec pertence"),
    title: z.string().describe("Título da Spec"),
    content: z.string().describe("Conteúdo da Spec em markdown"),
  },
  async (input) => json(await api.createSpec(input)),
);

server.tool(
  "approve_spec",
  "Aprova uma Spec que está em draft.",
  { spec_id: z.number().int() },
  async ({ spec_id }) => json(await api.approveSpec(spec_id)),
);

const transport = new StdioServerTransport();
await server.connect(transport);
