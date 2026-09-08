<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PrdOriginEnum: string implements HasLabel
{
    case Human = 'human';
    case Ai = 'ai';

    public function getLabel(): string
    {
        return match ($this) {
            self::Human => 'Humano',
            self::Ai => 'IA',
        };
    }
}
