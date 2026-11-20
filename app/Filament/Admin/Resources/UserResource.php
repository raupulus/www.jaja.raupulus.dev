<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Support\WebpImageUpload;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Unique;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $label = 'Usuario';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                WebpImageUpload::make('avatar', 'user-images', 'Avatar')
                    ->columnSpanFull()
                    ->alignCenter()
                    ->avatar()
                    ->imageResizeTargetHeight('250')
                    ->imageResizeTargetWidth('250')
                    ->imageResizeMode('crop'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    ->label('Email')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nick')
                    ->required()
                    ->label('Nick')
                    ->maxLength(25),
                Forms\Components\Select::make('role_id')
                    ->label('Rol')
                    ->options([
                        1 => 'Administrador',
                        2 => 'Usuario',
                    ])
                    ->default(2)
                    ->required()
                    ->native(false)
                    ->rule(function (?User $record): \Closure {
                        return function (string $attribute, $value, \Closure $fail) use ($record): void {
                            if ($record && (int) $record->role_id === 1 && (int) $value !== 1) {
                                $hasOtherAdmin = User::where('role_id', 1)
                                    ->whereNull('deleted_at')
                                    ->where('id', '!=', $record->id)
                                    ->exists();

                                if (! $hasOtherAdmin) {
                                    $fail('No puedes rebajar el rol del último administrador activo.');
                                }
                            }
                        };
                    }),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('Verificado')
                    ->hiddenOn(['create', 'edit'])
                    ->readOnly(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Contraseña')
                    ->hiddenOn('view')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->label('Avatar'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(35)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->limit(35)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role_id')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => (int) $state === 1 ? 'Administrador' : 'Usuario')
                    ->color(fn ($state): string => (int) $state === 1 ? 'danger' : 'info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Fecha de Borrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Rol')
                    ->options([
                        1 => 'Administrador',
                        2 => 'Usuario',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
