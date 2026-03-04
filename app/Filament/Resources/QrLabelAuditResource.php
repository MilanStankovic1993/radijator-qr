<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrLabelAuditResource\Pages;
use App\Models\QrLabel;
use App\Models\QrLabelAudit;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QrLabelAuditResource extends Resource
{
    protected static ?string $model = QrLabelAudit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'QR Audit log';
    protected static ?string $modelLabel = 'QR audit';
    protected static ?string $pluralModelLabel = 'QR audit log';

    protected static function isSuperAdmin(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isSuperAdmin();
    }

    public static function canViewAny(): bool
    {
        return static::isSuperAdmin();
    }

    public static function canView($record): bool
    {
        return static::isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Vreme')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Akcija')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('label.id')
                    ->label('QR ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('label.po_number')
                    ->label('PO')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Korisnik')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User-Agent')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('before')
                    ->label('Pre')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '-')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('after')
                    ->label('Posle')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '-')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ filter po konkretnom QR-u (ovo je ključno za "Vidi istoriju")
                Tables\Filters\SelectFilter::make('qr_label_id')
                    ->label('QR nalepnica')
                    ->options(fn () => QrLabel::query()
                        ->orderByDesc('id')
                        ->limit(300) // dovoljno za UI; ako bude previše, preći na async/search
                        ->get()
                        ->mapWithKeys(fn (QrLabel $l) => [
                            $l->id => "#{$l->id}" . ($l->po_number ? " • {$l->po_number}" : '') . " • {$l->token}",
                        ])->all()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('action')
                    ->label('Akcija')
                    ->options([
                        'create' => 'create',
                        'update' => 'update',
                        'disable' => 'disable',
                        'enable' => 'enable',
                        'delete' => 'delete',
                        'restore' => 'restore',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Korisnik')
                    ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['label', 'user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrLabelAudits::route('/'),
            'view'  => Pages\ViewQrLabelAudit::route('/{record}'),
        ];
    }
}