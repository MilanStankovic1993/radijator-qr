<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Korisnici';
    protected static ?string $modelLabel = 'Korisnik';
    protected static ?string $pluralModelLabel = 'Korisnici';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        // super_admin može brisati, ali ne sme da obriše sam sebe
        $u = auth()->user();
        return ($u?->hasRole('super_admin') ?? false) && ($u?->id !== $record->id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Osnovno')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Ime i prezime')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                ]),

            Forms\Components\Section::make('Uloga')
                ->schema([
                    Forms\Components\Select::make('role_name')
                        ->label('Rola')
                        ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->dehydrated(false) // ne postoji kolona, syncujemo ručno
                        ->afterStateHydrated(function (Forms\Components\Select $component, ?User $record): void {
                            $component->state($record?->getRoleNames()->first());
                        }),
                ]),

            Forms\Components\Section::make('Lozinka')
                ->description('Ako ostaviš prazno, lozinka se ne menja.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Nova lozinka')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->rule(Password::min(10)->letters()->numbers())
                        ->helperText('Min 10 karaktera, slova i brojevi.')
                        ->same('password_confirmation'),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Potvrda lozinke')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->requiredWith('password'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ime')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rola')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kreiran')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => auth()->id() !== $record->id),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(false),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // ništa specijalno, ali ostavljamo hook ako kasnije zatreba
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}