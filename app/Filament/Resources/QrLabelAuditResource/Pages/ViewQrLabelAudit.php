<?php

namespace App\Filament\Resources\QrLabelAuditResource\Pages;

use App\Filament\Resources\QrLabelAuditResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewQrLabelAudit extends ViewRecord
{
    protected static string $resource = QrLabelAuditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Osnovno')
                ->columns(2)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Vreme')
                        ->dateTime('d.m.Y H:i:s'),

                    TextEntry::make('action')
                        ->label('Akcija')
                        ->badge(),

                    TextEntry::make('label.id')
                        ->label('QR ID')
                        ->placeholder('-'),

                    TextEntry::make('label.po_number')
                        ->label('PO')
                        ->placeholder('-'),

                    TextEntry::make('user.name')
                        ->label('Korisnik')
                        ->placeholder('-'),

                    TextEntry::make('ip_address')
                        ->label('IP')
                        ->placeholder('-'),

                    TextEntry::make('user_agent')
                        ->label('User-Agent')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make('Pre / Posle')
                ->columns(2)
                ->schema([
                    TextEntry::make('before')
                        ->label('Pre')
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '-')
                        ->extraAttributes(['style' => 'white-space: pre-wrap; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;'])
                        ->columnSpan(1),

                    TextEntry::make('after')
                        ->label('Posle')
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '-')
                        ->extraAttributes(['style' => 'white-space: pre-wrap; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;'])
                        ->columnSpan(1),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('openLabel')
                ->label('Otvori QR nalepnicu')
                ->icon('heroicon-o-qr-code')
                ->url(fn () => $this->record->label ? \App\Filament\Resources\QrLabelResource::getUrl('edit', ['record' => $this->record->label]) : null)
                ->openUrlInNewTab()
                ->visible(fn () => (bool) $this->record->label),

            Actions\Action::make('back')
                ->label('Nazad')
                ->icon('heroicon-o-arrow-left')
                ->url(fn () => QrLabelAuditResource::getUrl('index')),
        ];
    }
}