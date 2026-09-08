<?php

namespace App\Filament\Resources\Specs\Pages;

use App\Enums\ArtifactStatusEnum;
use App\Filament\Resources\Specs\SpecResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpec extends CreateRecord
{
    protected static string $resource = SpecResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = ArtifactStatusEnum::Draft->value;

        return $data;
    }
}
