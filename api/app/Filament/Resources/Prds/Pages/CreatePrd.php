<?php

namespace App\Filament\Resources\Prds\Pages;

use App\Enums\ArtifactStatusEnum;
use App\Filament\Resources\Prds\PrdResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrd extends CreateRecord
{
    protected static string $resource = PrdResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = ArtifactStatusEnum::Draft->value;

        return $data;
    }
}
