<?php

namespace App\Filament\Resources\Prds;

use App\Filament\Resources\Prds\Pages\CreatePrd;
use App\Filament\Resources\Prds\Pages\EditPrd;
use App\Filament\Resources\Prds\Pages\ListPrds;
use App\Filament\Resources\Prds\Schemas\PrdForm;
use App\Filament\Resources\Prds\Tables\PrdsTable;
use App\Models\Prd;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrdResource extends Resource
{
    protected static ?string $model = Prd::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PrdForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrdsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrds::route('/'),
            'create' => CreatePrd::route('/create'),
            'edit' => EditPrd::route('/{record}/edit'),
        ];
    }
}
