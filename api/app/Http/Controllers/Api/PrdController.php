<?php

namespace App\Http\Controllers\Api;

use App\Enums\ArtifactStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Prd\StoreRequest;
use App\Http\Resources\PrdResource;
use App\Models\Prd;
use Illuminate\Http\JsonResponse;

class PrdController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $prd = Prd::create([
            ...$request->validated(),
            'status' => ArtifactStatusEnum::Draft,
        ])->refresh();

        return response()->json(['data' => new PrdResource($prd)], 201);
    }

    public function approve(Prd $prd): JsonResponse
    {
        $prd->update(['status' => ArtifactStatusEnum::Approved]);

        return response()->json(['data' => new PrdResource($prd)]);
    }
}
