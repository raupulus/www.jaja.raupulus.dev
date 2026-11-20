<?php

namespace App\Filament\Panel\Resources;

use App\Filament\Panel\Resources\ContentResource\Pages;
use App\Filament\Panel\Resources\ContentResource\RelationManagers\OptionsRelationManager;
use App\Filament\Support\WebpImageUpload;
use App\Models\Content;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static ?string $navigationLabel = 'Mis Entradas';

    protected static ?string $label = 'Mi Entrada';

    protected static ?string $navigationGroup = 'Mis Entradas';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $recordTitleAttribute = 'title';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido')
                    ->schema([
                        WebpImageUpload::make('image', 'content-images')
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
                            ->columnSpanFull()
                            ->maxLength(1024),
                    ]),

                Forms\Components\Section::make('Clasificación')
                    ->schema([
                        Forms\Components\Select::make('group_id')
                            ->relationship('group', 'title')
                            ->columnSpanFull()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Grupo')
                            ->placeholder('Seleccione un grupo'),

                        Forms\Components\Select::make('categories')
                            ->label('Categorías')
                            ->multiple()
                            ->relationship('categories', 'title')
                            ->preload()
                            ->searchable()
                            ->default([1])
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Si no hay categorías seleccionadas, asignar la categoría "General" con id 1
                                if (empty($state)) {
                                    $set('categories', [1]);
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Si no hay categorías seleccionadas, asignar la categoría "General" por defecto
                                if (empty($state)) {
                                    return [1];
                                }
                                return $state;
                            })
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Moderación')
                    ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                /*
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                */
                Tables\Columns\TextColumn::make('group.title')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Contenido')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupo')
                    ->relationship('group', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Categoría')
                    ->relationship('categories', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_adult')
                    ->label('Contenido Adulto'),
                Tables\Filters\TernaryFilter::make('is_ai')
                    ->label('Generado con IA'),
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
            OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        return $record->user_id === auth()->id();
    }

    public static function canView(Model $record): bool
    {
        return $record->user_id === auth()->id();
    }

    public static function canDelete(Model $record): bool
    {
        return $record->user_id === auth()->id();
    }


}
