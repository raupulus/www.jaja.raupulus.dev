<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CollaboratorResource\Pages;
use App\Filament\Admin\Resources\CollaboratorResource\RelationManagers;
use App\Filament\Support\WebpImageUpload;
use App\Models\Collaborator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CollaboratorResource extends Resource
{
    protected static ?string $model = Collaborator::class;

    protected static ?string $navigationLabel = 'Colaboradores';

    protected static ?string $label = 'Colaborador';

    protected static ?string $pluralLabel = 'Colaboradores';

    protected static ?string $modelLabel = 'Colaborador';

    protected static ?string $pluralModelLabel = 'Colaboradores';

    protected static ?string $title = 'Colaboradores';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                WebpImageUpload::make('image', 'collaborator-images')
                    ->columnSpanFull()
                    ->imageResizeTargetHeight(400)
                    ->imageResizeTargetWidth(400)
                    ->imageResizeMode('crop'),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nick')
                    ->label('Nick')
                    ->required()
                    ->maxLength(25),
                Forms\Components\TextInput::make('website')
                    ->label('Sitio Web')
                    ->rules(['url:http,https'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('url_repositories')
                    ->label('Enlace a perfil de repositorios (Github, Gitlab, etc.)')
                    ->rules(['url:http,https'])
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->relationship('user', 'name'),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull()
                    ->rows(3)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->limit(40)
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Sitio Web')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollaborators::route('/'),
            'create' => Pages\CreateCollaborator::route('/create'),
            'edit' => Pages\EditCollaborator::route('/{record}/edit'),
        ];
    }
}
