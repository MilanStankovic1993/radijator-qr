<?php

namespace App\Filament\Resources\ServiceQrLabelResource\Pages;

use App\Filament\Resources\ServiceQrLabelResource;
use App\Http\Controllers\ServiceQrLabelPublicController;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Route;

class EditServiceQrLabel extends EditRecord
{
    protected static string $resource = ServiceQrLabelResource::class;

    protected function getHeaderActions(): array
    {
        $documentLink = Route::has('service-qr-labels.public.show')
            ? route('service-qr-labels.public.show', (string) $this->record->token)
            : null;

        $documentLinkJs = $documentLink ? addslashes($documentLink) : '';

        return [
            Actions\Action::make('printedStatus')
                ->label(fn () => $this->record->printed_at ? 'Odstampano' : 'Neodstampano')
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
                        ->title('Status stampe je azuriran.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('document')
                ->label('Dokument')
                ->icon('heroicon-o-link')
                ->visible(fn (): bool => Route::has('service-qr-labels.public.show'))
                ->url(fn () => route('service-qr-labels.public.show', $this->record->token))
                ->openUrlInNewTab(),

            Actions\Action::make('zebraPrint')
                ->label('Zebra Print')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->visible(fn (): bool => Route::has('service-qr-labels.public.print-direct'))
                ->requiresConfirmation()
                ->modalHeading('Potvrda Zebra stampe')
                ->modalDescription('Da li zelis da oznacis ovu etiketu kao odstampanu i posaljes je direktno na Zebra stampac?')
                ->action(function () {
                    $result = app(ServiceQrLabelPublicController::class)->sendToPrinter($this->record);

                    $this->refreshFormData([
                        'printed_at',
                        'printed_by',
                    ]);

                    if (! ($result['ok'] ?? false)) {
                        Notification::make()
                            ->title($result['message'] ?? 'Stampa nije uspela.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title($result['message'] ?? 'Etiketa je poslata na Zebra stampac.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('htmlPrint')
                ->label('HTML Print')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->visible(fn (): bool => Route::has('service-qr-labels.public.print'))
                ->url(fn () => route('service-qr-labels.public.print', $this->record->token))
                ->openUrlInNewTab(),

            Actions\Action::make('copyLink')
                ->label('Copy link')
                ->icon('heroicon-o-clipboard-document')
                ->color('gray')
                ->visible(fn (): bool => Route::has('service-qr-labels.public.show'))
                ->action(function () use ($documentLinkJs) {
                    $this->js("navigator.clipboard.writeText('{$documentLinkJs}')");

                    Notification::make()
                        ->title('Link kopiran')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}