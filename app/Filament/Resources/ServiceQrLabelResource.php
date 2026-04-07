<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceQrLabelResource\Pages;
use App\Http\Controllers\ServiceQrLabelPublicController;
use App\Models\ServiceQrLabel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ServiceQrLabelResource extends Resource
{
    protected static ?string $model = ServiceQrLabel::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Servis QR kodovi';
    protected static ?string $modelLabel = 'servisni QR kod';
    protected static ?string $pluralModelLabel = 'Servis QR kodovi';
    protected static ?string $navigationGroup = 'Radijator';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('printed_at'),
            Forms\Components\Hidden::make('printed_by'),

            Forms\Components\Section::make('Osnovni podaci')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('picture_path')
                        ->label('Slika dela / Picture of part')
                        ->image()
                        ->imageEditor()
                        ->directory('service-qr-labels')
                        ->disk('public')
                        ->visibility('public')
                        ->columnSpanFull(),

                    Forms\Components\DatePicker::make('date')
                        ->label('Datum / Date')
                        ->native(false)
                        ->displayFormat('d.m.Y'),

                    Forms\Components\TextInput::make('supplier_order_number')
                        ->label('Broj narudžbenice dobavljača')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('name')
                        ->label('Naziv / Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('boiler_type')
                        ->label('Tip kotla / Type of boiler')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('dimension')
                        ->label('Dimenzija / Dimension')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('code_pdm')
                        ->label('CODE PDM')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('weight')
                        ->label('Težina / Weight (kg)')
                        ->numeric()
                        ->step('0.01')
                        ->suffix('kg'),

                    Forms\Components\TextInput::make('price')
                        ->label('Cena / Price (kom)')
                        ->numeric()
                        ->step('0.01')
                        ->prefix('RSD'),

                    Forms\Components\TextInput::make('buyer')
                        ->label('Kupac / Buyer')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Količina / Quantity')
                        ->numeric()
                        ->step('0.01'),
                ]),

            Forms\Components\Section::make('Napomena')
                ->schema([
                    Forms\Components\Textarea::make('note')
                        ->label('Napomena / Disc')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Sistem')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('token')
                        ->label('Token')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Automatski se generiše pri kreiranju.'),

                    Forms\Components\DateTimePicker::make('printed_at')
                        ->label('Odštampano')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\DateTimePicker::make('disabled_at')
                        ->label('Disable datum')
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->collapsed(),

            Forms\Components\Hidden::make('created_by')
                ->default(fn () => Auth::id())
                ->dehydrated(fn ($state) => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('picture_path')
                    ->label('Slika')
                    ->disk('public')
                    ->square()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('active_status')
                    ->label('Status')
                    ->boolean()
                    ->getStateUsing(fn (ServiceQrLabel $record) => ! $record->isDisabled())
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderByRaw("CASE WHEN disabled_at IS NULL THEN 1 ELSE 0 END {$direction}"))
                    ->toggleable(),

                Tables\Columns\IconColumn::make('printed_status')
                    ->label('Štampano')
                    ->boolean()
                    ->getStateUsing(fn (ServiceQrLabel $record) => $record->printed_at !== null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Datum')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('supplier_order_number')
                    ->label('Br. narudžbenice dobavljača')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('boiler_type')
                    ->label('Tip kotla')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dimension')
                    ->label('Dimenzija')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('code_pdm')
                    ->label('CODE PDM')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('weight')
                    ->label('Težina (kg)')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? number_format((float) $state, 2, ',', '.') . ' kg' : '-'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Cena')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? number_format((float) $state, 2, ',', '.') : '-'),

                Tables\Columns\TextColumn::make('buyer')
                    ->label('Kupac')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Količina')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? number_format((float) $state, 2, ',', '.') : '-'),

                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('printed_at')
                    ->label('Odštampano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('printer.name')
                    ->label('Štampao')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreirano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Kreirao')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Menjao')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('not_printed')
                    ->label('Samo neštampane')
                    ->default()
                    ->query(fn (Builder $query) => $query->whereNull('printed_at')),

                Tables\Filters\Filter::make('printed')
                    ->label('Samo štampane')
                    ->query(fn (Builder $query) => $query->whereNotNull('printed_at')),

                Tables\Filters\Filter::make('enabled')
                    ->label('Samo enable')
                    ->query(fn (Builder $query) => $query->whereNull('disabled_at')),

                Tables\Filters\Filter::make('disabled')
                    ->label('Samo disable')
                    ->query(fn (Builder $query) => $query->whereNotNull('disabled_at')),

                Tables\Filters\Filter::make('created_at_range')
                    ->label('Datum kreiranja (opseg)')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od datuma')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Do datuma')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))
                    ),

                Tables\Filters\SelectFilter::make('created_by')
                    ->label('Kreirao')
                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('updated_by')
                    ->label('Menjao')
                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('printed_by')
                    ->label('Štampao')
                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('toggle')
                        ->label(fn (ServiceQrLabel $record) => $record->isDisabled() ? 'Enable' : 'Disable')
                        ->icon(fn (ServiceQrLabel $record) => $record->isDisabled() ? 'heroicon-o-check' : 'heroicon-o-x-mark')
                        ->color(fn (ServiceQrLabel $record) => $record->isDisabled() ? 'success' : 'danger')
                        ->requiresConfirmation()
                        ->action(function (ServiceQrLabel $record): void {
                            if ($record->isDisabled()) {
                                $record->enable();
                            } else {
                                $record->disable();
                            }
                        }),

                    Tables\Actions\Action::make('duplicate')
                        ->label('Kopiraj')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->tooltip('Kreira novi servisni QR kod sa istim podacima (novi token).')
                        ->requiresConfirmation()
                        ->action(function (ServiceQrLabel $record) {
                            $new = $record->replicate();
                            $new->token = null;
                            $new->disabled_at = null;
                            $new->printed_at = null;
                            $new->printed_by = null;
                            $new->created_by = Auth::id();
                            $new->updated_by = null;
                            $new->save();

                            return redirect(static::getUrl('edit', ['record' => $new]));
                        }),

                    Tables\Actions\Action::make('document')
                        ->label('Dokument')
                        ->icon('heroicon-o-link')
                        ->visible(fn (): bool => Route::has('service-qr-labels.public.show'))
                        ->url(fn (ServiceQrLabel $record) => route('service-qr-labels.public.show', $record->token), true)
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('zplPrint')
                        ->label('Zebra Print')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->visible(fn (): bool => Route::has('service-qr-labels.public.print-direct'))
                        ->requiresConfirmation()
                        ->modalHeading('Potvrda Zebra stampe')
                        ->modalDescription('Da li zelis da oznacis ovu etiketu kao odstampanu i posaljes je direktno na Zebra stampac?')
                        ->action(function (ServiceQrLabel $record, $livewire) {
                            $result = app(ServiceQrLabelPublicController::class)->sendToPrinter($record);

                            $livewire->dispatch('$refresh');

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
                    Tables\Actions\Action::make('print')
                        ->label('HTML Print')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->visible(fn (): bool => Route::has('service-qr-labels.public.print'))
                        ->url(fn (ServiceQrLabel $record) => route('service-qr-labels.public.print', $record->token), true)
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('unprint')
                        ->label('Vrati u neštampane')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn (ServiceQrLabel $record): bool => $record->printed_at !== null)
                        ->requiresConfirmation()
                        ->action(function (ServiceQrLabel $record) {
                            $record->unmarkPrinted();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->label('Obriši')
                        ->requiresConfirmation(),
                ])
                    ->label('Opcije')
                    ->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->bulkActions([
Tables\Actions\BulkAction::make('preview_print_selected')
    ->label('Pregled / štampa označenih')
    ->icon('heroicon-o-printer')
    ->color('warning')
    ->requiresConfirmation()
    ->action(function ($records) {
        $ids = collect($records)
            ->pluck('id')
            ->implode(',');

        if (blank($ids)) {
            Notification::make()
                ->title('Niste odabrali nijednu stavku.')
                ->warning()
                ->send();

            return null;
        }

        return redirect()->to(route('service-qr-labels.preview-print', [
            'ids' => $ids,
        ]));
    }),
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Obriši označeno')
                    ->requiresConfirmation(),

                BulkAction::make('mark_printed')
                    ->label('Označi kao štampano')
                    ->icon('heroicon-o-printer')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->markAsPrinted();
                        }
                    }),

                BulkAction::make('mark_unprinted')
                    ->label('Vrati u neštampane')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->unmarkPrinted();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceQrLabels::route('/'),
            'create' => Pages\CreateServiceQrLabel::route('/create'),
            'edit' => Pages\EditServiceQrLabel::route('/{record}/edit'),
        ];
    }
}
