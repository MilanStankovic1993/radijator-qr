<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrLabelResource\Pages;
use App\Models\QrLabel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class QrLabelResource extends Resource
{
    protected static ?string $model = QrLabel::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR nalepnice';
    protected static ?string $modelLabel = 'QR nalepnica';
    protected static ?string $pluralModelLabel = 'QR nalepnice';
    protected static ?string $navigationGroup = 'Radijator';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Zajednički podaci')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('po_number')
                        ->label('Porudžbenica')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('vendor_no')
                        ->label('Broj dobavljača (Vendor No.)')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('buyer')
                        ->label('Kupac (Buyer)')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('storage_location')
                        ->label('Mesto prijema / skladištenja')
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('load_date')
                        ->label('Datum utovara')
                        ->native(false),

                    Forms\Components\TextInput::make('order_type')
                        ->label('Vrsta narudžbenice')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Količina')
                        ->numeric()
                        ->step('0.001'),

                    Forms\Components\TextInput::make('um')
                        ->label('Jedinica mere (UM)')
                        ->helperText('Npr: PC, KG, M...')
                        ->maxLength(20)
                        ->default('KG'),

                    Forms\Components\TextInput::make('price')
                        ->label('Cena')
                        ->numeric()
                        ->step('0.01'),
                ]),

            Forms\Components\Section::make('Fakturisanje / Isporuka i uslovi')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('billing_address')
                        ->label('Adresa za fakturisanje')
                        ->rows(4)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('billing_email')
                        ->label('E-mail za fakturisanje')
                        ->email()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('shipping_address')
                        ->label('Adresa za isporuku')
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('terms_payment')
                        ->label('Uslovi plaćanja')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('terms_delivery')
                        ->label('Uslovi isporuke')
                        ->maxLength(255),
                ]),

            Forms\Components\Section::make('Interni podaci - Radijator inženjering')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('ri_item_number')
                        ->label('Broj artikla (Radijator Inž)')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ri_code')
                        ->label('Šifra (Radijator Inž) - Sklop/Deo')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ri_name')
                        ->label('Naziv (Radijator Inž) - Sklop/Deo')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('ri_doc_number')
                        ->label('Prijemnica / otpremnica')
                        ->maxLength(255),
                ]),

            Forms\Components\Section::make('Group Atlantic')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('ga_item_number')
                        ->label('Broj artikla (Group Atlantic)')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ga_internal_number')
                        ->label('Interni broj')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ga_code')
                        ->label('Šifra (Group Atlantic) - Sklop/Deo')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('ga_name')
                        ->label('Naziv (Group Atlantic) - Sklop/Deo')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Napomena')
                ->schema([
                    Forms\Components\Textarea::make('note')
                        ->label('Napomena')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Hidden::make('created_by')
                ->default(fn () => Auth::id())
                ->dehydrated(fn ($state) => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\IconColumn::make('active_status')
                    ->label('Status')
                    ->boolean()
                    ->getStateUsing(fn (QrLabel $record) => ! $record->isDisabled())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('po_number')
                    ->label('Porudžbenica')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('vendor_no')
                    ->label('Vendor No.')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('buyer')
                    ->label('Kupac')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\TextColumn::make('storage_location')
                    ->label('Skladište')
                    ->wrap(),

                Tables\Columns\TextColumn::make('load_date')
                    ->label('Utovar')
                    ->date('d.m.Y'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Količina'),

                Tables\Columns\TextColumn::make('um')
                    ->label('UM')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('price')
                    ->label('Cena'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreirano')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Samo aktivne')
                    ->query(fn ($query) => $query->whereNull('disabled_at')),

                Tables\Filters\Filter::make('disabled')
                    ->label('Samo deaktivirane')
                    ->query(fn ($query) => $query->whereNotNull('disabled_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('toggle')
                    ->label(fn (QrLabel $record) => $record->isDisabled() ? 'Enable' : 'Disable')
                    ->icon(fn (QrLabel $record) => $record->isDisabled() ? 'heroicon-o-check' : 'heroicon-o-x-mark')
                    ->color(fn (QrLabel $record) => $record->isDisabled() ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (QrLabel $record): void {
                        if ($record->isDisabled()) {
                            $record->enable();
                        } else {
                            $record->disable();
                        }
                    }),

                Tables\Actions\Action::make('document')
                    ->label('Dokument')
                    ->icon('heroicon-o-link')
                    ->url(fn (QrLabel $record) => route('qr-labels.public.show', $record->token), true)
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (QrLabel $record) => route('qr-labels.public.print', $record->token), true)
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrLabels::route('/'),
            'create' => Pages\CreateQrLabel::route('/create'),
            'edit' => Pages\EditQrLabel::route('/{record}/edit'),
        ];
    }
}