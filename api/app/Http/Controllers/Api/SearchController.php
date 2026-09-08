<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrdResource;
use App\Http\Resources\SpecResource;
use App\Models\Prd;
use App\Models\Spec;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'project_slug' => ['nullable', 'string'],
        ]);

        $query = $request->string('query');

        $prds = Prd::query()
            ->when($request->filled('project_slug'), function ($builder) use ($request) {
                $builder->whereHas('project', fn ($project) => $project->where('slug', $request->string('project_slug')));
            })
            ->where(fn ($builder) => $builder->where('title', 'ilike', "%{$query}%")->orWhere('content', 'ilike', "%{$query}%"))
            ->latest()
            ->get();

        $specs = Spec::query()
            ->when($request->filled('project_slug'), function ($builder) use ($request) {
                $builder->whereHas('prd.project', fn ($project) => $project->where('slug', $request->string('project_slug')));
            })
            ->where(fn ($builder) => $builder->where('title', 'ilike', "%{$query}%")->orWhere('content', 'ilike', "%{$query}%"))
            ->latest()
            ->get();

        return response()->json([
            'data' => [
                'prds' => PrdResource::collection($prds),
                'specs' => SpecResource::collection($specs),
            ],
        ]);
    }
}
