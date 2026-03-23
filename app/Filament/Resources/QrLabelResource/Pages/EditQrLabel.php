<?php

namespace App\Filament\Resources\QrLabelResource\Pages;

use App\Filament\Resources\QrLabelResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQrLabel extends EditRecord
{
    protected static string $resource = QrLabelResource::class;

    protected function getHeaderActions(): array
    {
        $link = route('qr-labels.public.show', (string) $this->record->token);
        $linkJs = addslashes($link);

        return [
            Actions\Action::make('printedStatus')
                ->label(fn () => $this->record->printed_at ? 'Odštampano' : 'Neodštampano')
                ->color(fn () => $this->record->printed_at ? 'success' : 'gray')
                ->icon(fn () => $this->record->printed_at ? 'heroicon-o-check-circle' : 'heroicon-o-clock')
                ->action(function () {
                    if ($this->record->printed_at) {
                        $this->record->unmarkPrinted();
                    } else {
                        $this->record->markAsPrinted();
                    }

                    $this->refreshFormData([
                        'printed_at',
                        'printed_by',
                    ]);

                    Notification::make()
                        ->title('Status štampe je ažuriran.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('document')
                ->label('Dokument')
                ->icon('heroicon-o-link')
                ->url(fn () => route('qr-labels.public.show', $this->record->token))
                ->openUrlInNewTab(),

            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Potvrda štampe')
                ->modalDescription('Da li želiš da označiš ovu etiketu kao odštampanu i otvoriš print stranicu u novoj kartici?')
                ->action(function () {
                    $this->record->markAsPrinted();

                    $this->refreshFormData([
                        'printed_at',
                        'printed_by',
                    ]);

                    Notification::make()
                        ->title('Etiketa je označena kao odštampana.')
                        ->success()
                        ->send();

                    $url = route('qr-labels.public.print', $this->record->token);
                    $this->js("window.open('{$url}', '_blank')");
                }),

            Actions\Action::make('copyLink')
                ->label('Copy link')
                ->icon('heroicon-o-clipboard-document')
                ->color('gray')
                ->action(function () use ($linkJs) {
                    $this->js("navigator.clipboard.writeText('{$linkJs}')");

                    Notification::make()
                        ->title('Link kopiran')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}