<?php

namespace App\Filament\Panel\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Panel\Resources\CollaboratorResource\Pages;
use App\Filament\Panel\Resources\CollaboratorResource\RelationManagers;
use App\Models\Collaborator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CollaboratorResource extends Resource
{
    protected static ?string $model = Collaborator::class;

    protected static ?string $navigationLabel = 'Perfil Colaborador';

    protected static ?string $label = 'Perfil Colaborador';

    protected static ?string $pluralLabel = 'Perfil Colaborador';

    protected static ?string $modelLabel = 'Perfil Colaborador';

    protected static ?string $pluralModelLabel = 'Perfil Colaborador';

    protected static ?string $title = 'Perfil Colaborador';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

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
                    ->imageResizeMode('crop')
                    ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                        if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            try {
                                $tempFile = $state->getRealPath();
                                $converter = new ConvertImageToWebp();
                                $newPath = $converter($tempFile, 'collaborator-images');

                                if ($newPath && $newPath !== $tempFile) {
                                    $component->state([$newPath]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error en la conversión: ' . $e->getMessage());
                                \Log::error($e->getTraceAsString());
                            }
                        }
                    }),
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
                    ->rules(['url'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('url_repositories')
                    ->label('Enlace a perfil de repositorios (Github, Gitlab, etc.)')
                    ->rules(['url'])
                    ->maxLength(255),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Sitio Web')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Remuevo acciones en lote para usuarios normales
            ])
            ->emptyStateHeading('No tienes un perfil de colaborador')
            ->emptyStateDescription('Crea tu perfil para empezar a gestionar tus proyectos')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear mi perfil')
                    ->icon('heroicon-o-plus'),
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

    public static function canView($record): bool
    {
        return $record->user_id === auth()->id();
    }

    public static function canEdit($record): bool
    {
        return $record->user_id === auth()->id();
    }

    public static function canDelete($record): bool
    {
        return false; ## Los usuarios no pueden eliminar su perfil desde aquí
    }
}
