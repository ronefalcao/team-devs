<?php

namespace App\Filament\Resources\Specs\Pages;

use App\Filament\Resources\Specs\SpecResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpec extends EditRecord
{
    protected static string $resource = SpecResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
