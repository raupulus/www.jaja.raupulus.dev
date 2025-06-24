<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\CollaboratorResource\Pages;
use App\Filament\Admin\Resources\CollaboratorResource\RelationManagers;
use App\Models\Collaborator;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->columnSpanFull()
                    ->image()
                    ->disk('public')
                    ->directory('collaborator-images')
                    ->visibility('public')
                    ->imageEditor()
                    ->label('Imagen')
                    ->default(null)
                    ->imageResizeTargetHeight(400)
                    ->imageResizeTargetWidth(400)
                    ->imageResizeMode('crop', )
                    ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                        if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            try {
                                $tempFile = $state->getRealPath();
                                $converter = new ConvertImageToWebp();
                                $newPath = $converter($tempFile, 'collaborator-images');

                                if ($newPath && $newPath !== $tempFile) {
                                    ## Actualizo el estado con un array en lugar de un string
                                    $component->state([$newPath]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error en la conversión: ' . $e->getMessage());
                                \Log::error($e->getTraceAsString());
                            }
                        }
                    })
                ,
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nick')
                    ->required()
                    ->maxLength(25),
                Forms\Components\TextInput::make('website')
                    ->rules(['url'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('url_repositories')
                    ->label('Enlace a perfil de repositorios (Github, Gitlab, etc.)')
                    ->rules(['url'])
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->columnSpanFull()
                    ->relationship('user', 'name'),
                Forms\Components\Textarea::make('description')
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Sitio Web')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
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
                //
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
