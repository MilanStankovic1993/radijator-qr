<?php

namespace App\Filament\Resources;

use App\Exports\QrLabelsExport;
use App\Filament\Resources\QrLabelResource\Pages;
use App\Models\QrItemMapping;
use App\Models\QrLabel;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class QrLabelResource extends Resource
{
    protected static ?string $model = QrLabel::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR nalepnice';
    protected static ?string $modelLabel = 'QR nalepnica';
    protected static ?string $pluralModelLabel = 'QR nalepnice';
    protected static ?string $navigationGroup = 'Radijator';

    protected static function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected static function exportColumns(): array
    {
        return [
            'storage_location' => 'Skladište',
            'price' => 'Cena',
            'quantity' => 'Količina',
            'po_number' => 'Porudžbenica',
            'buyer' => 'Kupac',

            'vendor_no' => 'Vendor No.',
            'load_date' => 'Datum utovara',
            'um' => 'UM',
            'order_type' => 'Vrsta narudžbenice',

            'ri_item_number' => 'Broj artikla (Radijator Inž)',
            'ri_name' => 'Naziv (Radijator Inž)',
            'ri_doc_number' => 'Prijemnica / otpremnica (RI)',

            'ga_item_number' => 'Broj artikla (Group Atlantic)',
            'ga_code' => 'Šifra (Group Atlantic)',
            'ga_name' => 'Naziv (Group Atlantic) - Sklop/Deo',

            'token' => 'Token',
            'created_at' => 'Kreirano',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('printed_at'),
            Forms\Components\Hidden::make('printed_by'),

            Forms\Components\Section::make('Zajednički podaci')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('po_number')->label('Porudžbenica')->maxLength(255),
                    Forms\Components\TextInput::make('vendor_no')->label('Broj dobavljača (Vendor No.)')->maxLength(255),
                    Forms\Components\TextInput::make('buyer')->label('Kupac (Buyer)')->maxLength(255),
                    Forms\Components\TextInput::make('storage_location')->label('Mesto prijema / skladištenja')->maxLength(255),

                    Forms\Components\DatePicker::make('load_date')->label('Datum utovara')->native(false),

                    Forms\Components\TextInput::make('order_type')->label('Vrsta narudžbenice')->maxLength(255),

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
                    Forms\Components\Textarea::make('billing_address')->label('Adresa za fakturisanje')->rows(4)->columnSpan(1),
                    Forms\Components\TextInput::make('billing_email')->label('E-mail za fakturisanje')->email()->maxLength(255)->columnSpan(1),
                    Forms\Components\Textarea::make('shipping_address')->label('Adresa za isporuku')->rows(4)->columnSpanFull(),
                    Forms\Components\TextInput::make('terms_payment')->label('Uslovi plaćanja')->maxLength(255),
                    Forms\Components\TextInput::make('terms_delivery')->label('Uslovi isporuke')->maxLength(255),
                ]),

            Forms\Components\Section::make('Mapiranje artikla')
                ->schema([
                    Forms\Components\Select::make('qr_item_mapping_id')
                        ->label('Izaberi mapiranje artikla')
                        ->options(fn () => QrItemMapping::query()
                            ->orderBy('ri_item_number')
                            ->get()
                            ->mapWithKeys(fn (QrItemMapping $mapping) => [
                                $mapping->id => $mapping->displayName(),
                            ])
                            ->all()
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! $state) {
                                return;
                            }

                            $mapping = QrItemMapping::find($state);

                            if (! $mapping) {
                                return;
                            }

                            $set('ri_item_number', $mapping->ri_item_number);
                            $set('ri_name', $mapping->ri_name);
                            $set('ga_item_number', $mapping->ga_item_number);
                            $set('ga_code', $mapping->ga_code);
                            $set('ga_name', $mapping->ga_name);
                        })
                        ->createOptionForm([
                            Forms\Components\Section::make('Radijator inženjering')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('ri_item_number')
                                        ->label('Broj artikla (Radijator Inž)')
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('ri_name')
                                        ->label('Naziv (Radijator Inž) - Sklop/Deo')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Group Atlantic')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('ga_item_number')
                                        ->label('Broj artikla (Group Atlantic)')
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('ga_code')
                                        ->label('Šifra (Group Atlantic) - Sklop/Deo')
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('ga_name')
                                        ->label('Naziv (Group Atlantic) - Sklop/Deo')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->createOptionUsing(function (array $data) {
                            $mapping = QrItemMapping::create($data);

                            return $mapping->id;
                        })
                        ->helperText('Izaberi postojeće mapiranje ili klikni + da dodaš novo.'),
                ]),

            Forms\Components\Section::make('Interni podaci - Radijator inženjering')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('ri_item_number')->label('Broj artikla (Radijator Inž)')->maxLength(255),
                    Forms\Components\TextInput::make('ri_name')->label('Naziv (Radijator Inž) - Sklop/Deo')->maxLength(255)->columnSpanFull(),
                    Forms\Components\TextInput::make('ri_doc_number')->label('Prijemnica / otpremnica')->maxLength(255),
                ]),

            Forms\Components\Section::make('Group Atlantic')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('ga_item_number')->label('Broj artikla (Group Atlantic)')->maxLength(255),
                    Forms\Components\TextInput::make('ga_code')->label('Šifra (Group Atlantic) - Sklop/Deo')->maxLength(255),
                    Forms\Components\TextInput::make('ga_name')->label('Naziv (Group Atlantic) - Sklop/Deo')->maxLength(255)->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Napomena')
                ->schema([
                    Forms\Components\Textarea::make('note')->label('Napomena')->rows(4)->columnSpanFull(),
                ]),

            Forms\Components\Hidden::make('created_by')
                ->default(fn () => Auth::id())
                ->dehydrated(fn ($state) => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        $exportColumns = static::exportColumns();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed())
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('active_status')
                    ->label('Status')
                    ->boolean()
                    ->getStateUsing(fn (QrLabel $record) => ! $record->isDisabled())
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderByRaw("CASE WHEN disabled_at IS NULL THEN 1 ELSE 0 END {$direction}"))
                    ->toggleable(),

                Tables\Columns\IconColumn::make('printed_status')
                    ->label('Štampano')
                    ->boolean()
                    ->getStateUsing(fn (QrLabel $record) => $record->printed_at !== null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('po_number')->label('Porudžbenica')->searchable()->sortable()->toggleable()->wrap(),
                Tables\Columns\TextColumn::make('storage_location')->label('Skladište')->searchable()->sortable()->toggleable()->wrap(),
                Tables\Columns\TextColumn::make('load_date')->label('Utovar')->date('d.m.Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('quantity')->label('Količina')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('price')->label('Cena')->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('token')->label('Token')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('vendor_no')->label('Vendor No.')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)->wrap(),
                Tables\Columns\TextColumn::make('buyer')->label('Kupac')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)->wrap(),
                Tables\Columns\TextColumn::make('order_type')->label('Vrsta')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true)->wrap(),
                Tables\Columns\TextColumn::make('um')->label('UM')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ri_item_number')->label('RI artikal')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ri_name')->label('RI naziv')->searchable()->toggleable(isToggledHiddenByDefault: true)->wrap(),
                Tables\Columns\TextColumn::make('ri_doc_number')->label('RI dokument')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ga_item_number')->label('GA artikal')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ga_code')->label('GA šifra')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ga_name')->label('GA naziv')->searchable()->toggleable(isToggledHiddenByDefault: true)->wrap(),

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

                Tables\Columns\TextColumn::make('created_at')->label('Kreirano')->dateTime('d.m.Y H:i')->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')->label('Kreirao')->sortable()->toggleable(isToggledHiddenByDefault: true)->placeholder('-'),
                Tables\Columns\TextColumn::make('editor.name')->label('Menjao')->sortable()->toggleable(isToggledHiddenByDefault: true)->placeholder('-'),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Obrisano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-')
                    ->visible(fn () => static::isSuperAdmin()),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('not_deleted')
                    ->label('Aktivne')
                    ->visible(fn () => static::isSuperAdmin())
                    ->query(fn (Builder $query) => $query->withoutTrashed()),

                Tables\Filters\Filter::make('deleted')
                    ->label('Obrisane')
                    ->visible(fn () => static::isSuperAdmin())
                    ->query(fn (Builder $query) => $query->onlyTrashed()),

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

                Tables\Filters\Filter::make('load_date_range')
                    ->label('Datum utovara (opseg)')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od datuma')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Do datuma')->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('load_date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('load_date', '<=', $date))
                    ),

                Tables\Filters\Filter::make('quantity_range')
                    ->label('Količina (opseg)')
                    ->form([
                        Forms\Components\TextInput::make('min')->label('Količina od')->numeric(),
                        Forms\Components\TextInput::make('max')->label('Količina do')->numeric(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['min'] ?? null, fn (Builder $q, $v) => $q->where('quantity', '>=', $v))
                        ->when($data['max'] ?? null, fn (Builder $q, $v) => $q->where('quantity', '<=', $v))
                    ),

                Tables\Filters\Filter::make('price_range')
                    ->label('Cena (opseg)')
                    ->form([
                        Forms\Components\TextInput::make('min')->label('Cena od')->numeric(),
                        Forms\Components\TextInput::make('max')->label('Cena do')->numeric(),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['min'] ?? null, fn (Builder $q, $v) => $q->where('price', '>=', $v))
                        ->when($data['max'] ?? null, fn (Builder $q, $v) => $q->where('price', '<=', $v))
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
                    Tables\Actions\Action::make('history')
                        ->label('Vidi istoriju')
                        ->icon('heroicon-o-clock')
                        ->visible(fn () => static::isSuperAdmin())
                        ->url(fn (QrLabel $record) => \App\Filament\Resources\QrLabelAuditResource::getUrl('index', [
                            'tableFilters' => [
                                'qr_label_id' => ['value' => $record->id],
                            ],
                        ])),

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

                    Tables\Actions\Action::make('duplicate')
                        ->label('Kopiraj')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->tooltip('Kreira novu nalepnicu sa istim podacima (novi token).')
                        ->requiresConfirmation()
                        ->action(function (QrLabel $record) {
                            $new = $record->replicate();
                            $new->token = null;
                            $new->disabled_at = null;
                            $new->printed_at = null;
                            $new->printed_by = null;
                            $new->created_by = Auth::id();

                            if ($new->isFillable('updated_by')) {
                                $new->updated_by = null;
                            }

                            $new->save();

                            return redirect(static::getUrl('edit', ['record' => $new]));
                        }),

                    Tables\Actions\Action::make('document')
                        ->label('Dokument')
                        ->icon('heroicon-o-link')
                        ->url(fn (QrLabel $record) => route('qr-labels.public.show', $record->token), true)
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Potvrda štampe')
                        ->modalDescription('Da li želiš da označiš ovu etiketu kao odštampanu i otvoriš print stranicu u novoj kartici?')
                        ->action(function (QrLabel $record, $livewire) {
                            $record->markAsPrinted();

                            Notification::make()
                                ->title('Etiketa je označena kao odštampana.')
                                ->success()
                                ->send();

                            $url = route('qr-labels.public.print', $record->token);

                            $livewire->dispatch('$refresh');
                            $livewire->js("window.open('{$url}', '_blank')");
                        }),

                    Tables\Actions\Action::make('unprint')
                        ->label('Vrati u neštampane')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->visible(fn (QrLabel $record) => $record->printed_at !== null)
                        ->requiresConfirmation()
                        ->action(function (QrLabel $record) {
                            $record->unmarkPrinted();
                        }),

                    Tables\Actions\Action::make('restore')
                        ->label('Vrati')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn (QrLabel $record) => static::isSuperAdmin() && method_exists($record, 'trashed') && $record->trashed())
                        ->requiresConfirmation()
                        ->action(fn (QrLabel $record) => $record->restore()),

                    Tables\Actions\DeleteAction::make()
                        ->label('Obriši')
                        ->requiresConfirmation(),
                ])
                    ->label('Opcije')
                    ->icon('heroicon-o-ellipsis-vertical'),
            ])
            ->bulkActions([
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

                BulkAction::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        CheckboxList::make('columns')
                            ->label('Izaberite kolone za export')
                            ->options($exportColumns)
                            ->columns(2)
                            ->searchable()
                            ->default([
                                'storage_location',
                                'price',
                                'quantity',
                                'po_number',
                                'buyer',
                                'ri_item_number',
                                'ri_name',
                                'ga_item_number',
                                'ga_code',
                                'ga_name',
                            ])
                            ->required(),
                    ])
                    ->action(function ($records, array $data) use ($exportColumns) {
                        $picked = $data['columns'] ?? [];
                        $selected = array_intersect_key($exportColumns, array_flip($picked));

                        $user = auth()->user();
                        $username = $user?->name ?: 'korisnik';

                        $safeUsername = \Illuminate\Support\Str::slug($username, '_');
                        $stamp = now()->format('Y-m-d_H-i');
                        $fileName = "{$safeUsername}_{$stamp}.xlsx";

                        return Excel::download(
                            new QrLabelsExport($records, $selected),
                            $fileName
                        );
                    }),
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