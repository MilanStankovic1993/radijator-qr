<?php

namespace App\Filament\Resources\QrLabelResource\Pages;

use App\Filament\Resources\QrLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQrLabel extends EditRecord
{
    protected static string $resource = QrLabelResource::class;

    protected function getHeaderActions(): array
    {
        $link = route('qr-labels.public.show', (string) $this->record->token);
        $linkJs = addslashes($link);

        return [
            Actions\Action::make('document')
                ->label('Dokument')
                ->icon('heroicon-o-link')
                ->url(fn () => route('qr-labels.public.show', $this->record->token), true)
                ->openUrlInNewTab(),

            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('qr-labels.public.print', $this->record->token), true)
                ->openUrlInNewTab(),

            Actions\Action::make('copyLink')
                ->label('Copy link')
                ->icon('heroicon-o-clipboard-document')
                ->color('gray')
                ->extraAttributes([
                    'x-on:click' => "navigator.clipboard.writeText('{$linkJs}'); \$dispatch('notify', { message: 'Link kopiran' })",
                ]),

            Actions\DeleteAction::make(),
        ];
    }
}