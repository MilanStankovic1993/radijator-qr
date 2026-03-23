<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrItemMappingResource\Pages;
use App\Models\QrItemMapping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QrItemMappingResource extends Resource
{
    protected static ?string $model = QrItemMapping::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Mapiranje artikala';
    protected static ?string $modelLabel = 'mapiranje artikla';
    protected static ?string $pluralModelLabel = 'mapiranje artikala';
    protected static ?string $navigationGroup = 'Radijator';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ri_item_number')
                    ->label('RI artikal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ri_name')
                    ->label('RI naziv')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('ga_item_number')
                    ->label('GA artikal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ga_code')
                    ->label('GA šifra')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ga_name')
                    ->label('GA naziv')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreirano')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrItemMappings::route('/'),
            'create' => Pages\CreateQrItemMapping::route('/create'),
            'edit' => Pages\EditQrItemMapping::route('/{record}/edit'),
        ];
    }
}