<?php

namespace App\Filament\Resources\QrItemMappingResource\Pages;

use App\Filament\Resources\QrItemMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrItemMappings extends ListRecords
{
    protected static string $resource = QrItemMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
