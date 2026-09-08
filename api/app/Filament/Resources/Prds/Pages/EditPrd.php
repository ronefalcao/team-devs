<?php

namespace App\Filament\Resources\Prds\Pages;

use App\Filament\Resources\Prds\PrdResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrd extends EditRecord
{
    protected static string $resource = PrdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
