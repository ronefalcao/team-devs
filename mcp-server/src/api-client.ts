const API_BASE_URL = process.env.AISF_API_BASE_URL ?? "http://127.0.0.1:8000/api";
const API_TOKEN = process.env.AISF_API_TOKEN;

if (!API_TOKEN) {
  throw new Error(
    "AISF_API_TOKEN não definido. Configure a variável de ambiente com o mesmo valor de MCP_API_TOKEN do .env da API Laravel.",
  );
}

class ApiError extends Error {
  constructor(
    public status: number,
    public body: unknown,
  ) {
    super(`Erro na API AI Software Factory (HTTP ${status}): ${JSON.stringify(body)}`);
  }
}

async function request<T>(path: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers: {
      Authorization: `Bearer ${API_TOKEN}`,
      Accept: "application/json",
      "Content-Type": "application/json",
      ...init.headers,
    },
  });

  const body = await response.json().catch(() => null);

  if (!response.ok) {
    throw new ApiError(response.status, body);
  }

  return body as T;
}

export interface Project {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  stack: string | null;
}

export interface Module {
  id: number;
  name: string;
  slug: string;
  description: string | null;
}

export interface Spec {
  id: number;
  prd_id: number;
  title: string;
  content: string;
  version: number;
  status: "draft" | "approved" | "deprecated";
  created_at: string;
  updated_at: string;
}

export interface Prd {
  id: number;
  project_id: number;
  module_id: number | null;
  title: string;
  content: string;
  version: number;
  status: "draft" | "approved" | "deprecated";
  origin: "human" | "ai";
  specs?: Spec[];
  created_at: string;
  updated_at: string;
}

export interface ProjectContext {
  project: Project;
  modules: Module[];
  prds: Prd[];
}

export const api = {
  listProjects: () => request<{ data: Project[] }>("/projects").then((r) => r.data),

  getProjectContext: (projectSlug: string) =>
    request<{ data: ProjectContext }>(`/projects/${encodeURIComponent(projectSlug)}/context`).then((r) => r.data),

  searchKnowledge: (query: string, projectSlug?: string) => {
    const params = new URLSearchParams({ query });
    if (projectSlug) params.set("project_slug", projectSlug);

    return request<{ data: { prds: Prd[]; specs: Spec[] } }>(`/search?${params.toString()}`).then((r) => r.data);
  },

  createPrd: (input: { project_id: number; module_id?: number; title: string; content: string; origin: "human" | "ai" }) =>
    request<{ data: Prd }>("/prds", {
      method: "POST",
      body: JSON.stringify(input),
    }).then((r) => r.data),

  approvePrd: (prdId: number) =>
    request<{ data: Prd }>(`/prds/${prdId}/approve`, { method: "POST" }).then((r) => r.data),

  createSpec: (input: { prd_id: number; title: string; content: string }) =>
    request<{ data: Spec }>("/specs", {
      method: "POST",
      body: JSON.stringify(input),
    }).then((r) => r.data),

  approveSpec: (specId: number) =>
    request<{ data: Spec }>(`/specs/${specId}/approve`, { method: "POST" }).then((r) => r.data),
};
