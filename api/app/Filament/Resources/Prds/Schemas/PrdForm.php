<?php

namespace App\Filament\Resources\Prds\Schemas;

use App\Enums\ArtifactStatusEnum;
use App\Enums\PrdOriginEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                Select::make('module_id')
                    ->relationship('module', 'name'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->rows(20)
                    ->columnSpanFull(),
                TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options(ArtifactStatusEnum::class)
                    ->default('draft')
                    ->required()
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                Select::make('origin')
                    ->options(PrdOriginEnum::class)
                    ->required(),
            ]);
    }
}
