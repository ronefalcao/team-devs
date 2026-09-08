<?php

namespace App\Filament\Resources\Specs\Schemas;

use App\Enums\ArtifactStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SpecForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('prd_id')
                    ->relationship('prd', 'title')
                    ->required(),
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
            ]);
    }
}
