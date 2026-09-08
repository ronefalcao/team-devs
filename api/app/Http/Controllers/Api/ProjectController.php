<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\PrdResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => ProjectResource::collection(Project::orderBy('name')->get()),
        ]);
    }

    public function context(Project $project): JsonResponse
    {
        $project->load(['modules', 'prds.specs']);

        return response()->json([
            'data' => [
                'project' => new ProjectResource($project),
                'modules' => ModuleResource::collection($project->modules),
                'prds' => PrdResource::collection($project->prds),
            ],
        ]);
    }
}
