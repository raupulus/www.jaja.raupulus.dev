<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuggestionResource\Pages;
use App\Filament\Admin\Resources\SuggestionResource\RelationManagers\OptionsRelationManager;
use App\Filament\Support\WebpImageUpload;
use App\Models\Group;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $label = 'Sugerencia';
    protected static ?string $pluralLabel = 'Sugerencias';

    protected static ?string $navigationLabel = 'Sugerencias';
    protected static ?string $navigationGroup = 'Moderación';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido')
                    ->schema([
                        WebpImageUpload::make('image_path', 'suggestion-images')
                            ->columnSpanFull()
                            ->imageResizeTargetHeight(768)
                            ->imageResizeTargetWidth(1024)
                            ->imageResizeMode('crop'),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content')
                            ->label('Contenido')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Clasificación y Moderación')
                    ->schema([
                        Forms\Components\Select::make('type_id')
                            ->label('Tipo')
                            ->searchable()
                            ->relationship('type', 'name')
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set) {
                                ## Cuando cambia el tipo, reseteo el grupo
                                $set('group_id', null);
                            }),

                        Forms\Components\Select::make('group_id')
                            ->label('Grupo')
                            ->searchable()
                            ->options(function (Forms\Get $get) {
                                $typeId = $get('type_id');

                                if (! $typeId) {
                                    return [];
                                }

                                ## Grupos que pertenecen al tipo seleccionado
                                return Group::where('type_id', $typeId)
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->disabled(fn (Forms\Get $get) => ! $get('type_id')),

                        Forms\Components\Select::make('categories')
                            ->label('Categorías')
                            ->multiple()
                            ->relationship('categories', 'title')
                            ->preload()
                            ->searchable()
                            ->default([1])
                            ->dehydrateStateUsing(function ($state) {
                                // Siempre asegurar que hay al menos la categoría General
                                if (empty($state) || $state === null || (is_array($state) && count($state) === 0)) {
                                    return [1];
                                }
                                return $state;
                            })
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Si el usuario borra todas las categorías, automáticamente asignar General
                                if (empty($state) || $state === null) {
                                    $set('categories', [1]);
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Checkbox::make('is_adult')
                            ->label('Contenido Adulto')
                            ->default(false)
                            ->helperText('⚠️ ADVERTENCIA: Subir contenido adulto sin marcar esta casilla puede ser motivo de baneo permanente de la plataforma.')
                            ->extraAttributes([
                                'class' => 'border-red-500'
                            ]),

                        Forms\Components\Checkbox::make('is_ai')
                            ->label('Generado con IA')
                            ->default(false)
                            ->helperText('🤖 IMPORTANTE: Subir contenido generado por IA sin marcar esta casilla puede resultar en la suspensión de tu cuenta.')
                            ->extraAttributes([
                                'class' => 'border-orange-500'
                            ]),
                    ])
                    ->columns(['default' => 1, 'md' => 2]),

                Forms\Components\Section::make('Metadatos de Envío')
                    ->schema([
                        Forms\Components\TextInput::make('nick')
                            ->label('Nick')
                            ->maxLength(25),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP')
                            ->readOnly(),
                        Forms\Components\TextInput::make('user_agent')
                            ->label('Agente de Usuario')
                            ->readOnly()
                            ->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'md' => 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Imagen'),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Agente de Usuario')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
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
            ->defaultSort('created_at')
            ->filters([
                Tables\Filters\SelectFilter::make('type_id')
                    ->label('Tipo')
                    ->relationship('type', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupo')
                    ->relationship('group', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_adult')
                    ->label('Contenido Adulto'),
                Tables\Filters\TernaryFilter::make('is_ai')
                    ->label('Generado con IA'),
                Tables\Filters\TernaryFilter::make('approved_at')
                    ->label('Aprobada')
                    ->nullable(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            OptionsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Suggestion::whereNull('deleted_at')->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuggestions::route('/'),
            //'create' => Pages\CreateSuggestion::route('/create'),
            'edit' => Pages\EditSuggestion::route('/{record}/edit'),
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
