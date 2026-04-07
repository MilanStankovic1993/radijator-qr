<?php

namespace App\Filament\Resources\ServiceQrLabelResource\Pages;

use App\Filament\Resources\ServiceQrLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceQrLabels extends ListRecords
{
    protected static string $resource = ServiceQrLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}