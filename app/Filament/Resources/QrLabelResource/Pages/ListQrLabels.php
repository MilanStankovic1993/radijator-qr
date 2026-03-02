<?php

namespace App\Filament\Resources\QrLabelResource\Pages;

use App\Filament\Resources\QrLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrLabels extends ListRecords
{
    protected static string $resource = QrLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
