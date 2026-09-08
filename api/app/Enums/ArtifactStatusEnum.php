<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ArtifactStatusEnum: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Deprecated = 'deprecated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Approved => 'Aprovado',
            self::Deprecated => 'Descontinuado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Approved => 'success',
            self::Deprecated => 'danger',
        };
    }
}
