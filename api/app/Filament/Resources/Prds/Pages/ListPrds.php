<?php

namespace App\Filament\Resources\Prds\Pages;

use App\Filament\Resources\Prds\PrdResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrds extends ListRecords
{
    protected static string $resource = PrdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
