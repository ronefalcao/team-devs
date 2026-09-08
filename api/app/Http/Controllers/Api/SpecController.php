<?php

namespace App\Http\Controllers\Api;

use App\Enums\ArtifactStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Spec\StoreRequest;
use App\Http\Resources\SpecResource;
use App\Models\Spec;
use Illuminate\Http\JsonResponse;

class SpecController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $spec = Spec::create([
            ...$request->validated(),
            'status' => ArtifactStatusEnum::Draft,
        ])->refresh();

        return response()->json(['data' => new SpecResource($spec)], 201);
    }

    public function approve(Spec $spec): JsonResponse
    {
        $spec->update(['status' => ArtifactStatusEnum::Approved]);

        return response()->json(['data' => new SpecResource($spec)]);
    }
}
