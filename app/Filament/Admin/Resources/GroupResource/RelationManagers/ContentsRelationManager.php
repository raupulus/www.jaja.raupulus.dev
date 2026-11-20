<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use App\Filament\Support\WebpImageUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContentsRelationManager extends RelationManager
{
    protected static string $relationship = 'contents';

    protected static ?string $title = 'Entradas';

    protected static ?string $label = 'Entrada';

    protected static ?string $pluralLabel = 'Entradas';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $icon = 'heroicon-o-document-text';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                WebpImageUpload::make('image', 'content-images')
                    ->columnSpanFull()
                    ->imageResizeTargetHeight('768')
                    ->imageResizeTargetWidth('1024')
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

                Forms\Components\Select::make('categories')
                    ->label('Categorías')
                    ->multiple()
                    ->relationship('categories', 'title')
                    ->preload()
                    ->searchable()
                    ->default([1])
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        // # Si no hay categorías seleccionadas, asigno la categoría "General" con id 1
                        if (empty($state)) {
                            $set('categories', [1]);
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // # Si no hay categorías seleccionadas, asigno la categoría "General" por defecto
                        if (empty($state)) {
                            return [1];
                        }

                        return $state;
                    })
                    ->columnSpanFull(),

                Forms\Components\Checkbox::make('is_adult')
                    ->label('Contenido Adulto')
                    ->default(false)
                    ->helperText('⚠️ ADVERTENCIA: Subir contenido adulto sin marcar esta casilla puede ser motivo de baneo permanente de la plataforma.')
                    ->extraAttributes([
                        'class' => 'border-red-500',
                    ]),

                Forms\Components\Checkbox::make('is_ai')
                    ->label('Generado con IA')
                    ->default(false)
                    ->helperText('🤖 IMPORTANTE: Subir contenido generado por IA sin marcar esta casilla puede resultar en la suspensión de tu cuenta.')
                    ->extraAttributes([
                        'class' => 'border-orange-500',
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
