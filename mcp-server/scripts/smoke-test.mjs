import { Client } from "@modelcontextprotocol/sdk/client/index.js";
import { StdioClientTransport } from "@modelcontextprotocol/sdk/client/stdio.js";

const apiToken = process.env.AISF_API_TOKEN;
if (!apiToken) {
  console.error("Defina AISF_API_TOKEN antes de rodar este script.");
  process.exit(1);
}

const transport = new StdioClientTransport({
  command: "node",
  args: ["dist/index.js"],
  env: {
    AISF_API_BASE_URL: process.env.AISF_API_BASE_URL ?? "http://127.0.0.1:8000/api",
    AISF_API_TOKEN: apiToken,
  },
});

const client = new Client({ name: "smoke-test", version: "1.0.0" });
await client.connect(transport);

const tools = await client.listTools();
console.log("=== tools disponíveis ===");
console.log(tools.tools.map((t) => t.name).join(", "));

console.log("\n=== create_prd ===");
const created = await client.callTool({
  name: "create_prd",
  arguments: {
    project_id: 1,
    title: "PRD smoke-test via MCP",
    content: "Conteúdo criado pelo smoke-test.mjs para validar o fluxo MCP -> API.",
    origin: "ai",
  },
});
console.log(created.content[0].text);
const prd = JSON.parse(created.content[0].text);

console.log("\n=== get_project_context ===");
const context = await client.callTool({
  name: "get_project_context",
  arguments: { project_slug: "smoke-test-project" },
});
console.log(context.content[0].text);

console.log("\n=== approve_prd ===");
const approved = await client.callTool({
  name: "approve_prd",
  arguments: { prd_id: prd.id },
});
console.log(approved.content[0].text);

console.log("\n=== search_knowledge ===");
const search = await client.callTool({
  name: "search_knowledge",
  arguments: { query: "smoke-test" },
});
console.log(search.content[0].text);

await client.close();
console.log("\nOK: todas as tools responderam.");
process.exit(0);
