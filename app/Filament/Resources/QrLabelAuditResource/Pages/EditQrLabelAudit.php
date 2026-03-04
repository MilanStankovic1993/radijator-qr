<?php

namespace App\Filament\Resources\QrLabelAuditResource\Pages;

use App\Filament\Resources\QrLabelAuditResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQrLabelAudit extends EditRecord
{
    protected static string $resource = QrLabelAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
