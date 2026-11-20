<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\ContentResource\Pages;
use App\Filament\Admin\Resources\ContentResource\RelationManagers;
use App\Helpers\PublishToSocialHelper;
use App\Models\Content;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static ?string $navigationLabel = 'Entradas';

    protected static ?string $label = 'Entrada';

    protected static ?string $navigationGroup = 'Contenidos';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->columnSpanFull()
                            ->image()
                            ->disk('public')
                            ->directory('content-images')
                            ->visibility('public')
                            ->imageEditor()
                            ->label('Imagen')
                            ->default(null)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imageResizeTargetHeight(768)
                            ->imageResizeTargetWidth(1024)
                            ->imageResizeMode('crop', )
                            ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                                if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                    try {
                                        $tempFile = $state->getRealPath();
                                        $converter = new ConvertImageToWebp();
                                        $newPath = $converter($tempFile, 'content-images');

                                        if ($newPath && $newPath !== $tempFile) {
                                            ## Actualizo el estado con un array en lugar de un string
                                            $component->state([$newPath]);
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Error en la conversión: ' . $e->getMessage());
                                        \Log::error($e->getTraceAsString());
                                    }
                                }
                            }),
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
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Usuario')
                            ->default(fn () => auth()->id())
                            ->placeholder('Seleccione un usuario'),

                        Forms\Components\Select::make('group_id')
                            ->relationship('group', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Grupo')
                            ->placeholder('Seleccione un grupo')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Título'),
                                Forms\Components\TextInput::make('slug')
                                    ->columnSpanFull()
                                    ->label('Slug')
                                    ->required()
                                    ->unique('groups', 'slug', ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\Select::make('type_id')
                                    ->relationship('type', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Tipo')
                                    ->placeholder('Seleccione un tipo'),
                            ]),

                        Forms\Components\Select::make('categories')
                            ->label('Categorías')
                            ->multiple()
                            ->relationship('categories', 'title')
                            ->preload()
                            ->searchable()
                            ->default([1])
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                ## Si no hay categorías seleccionadas, asigno la categoría "General" con id 1
                                if (empty($state)) {
                                    $set('categories', [1]);
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                ## Si no hay categorías seleccionadas, asigno la categoría "General" por defecto
                                if (empty($state)) {
                                    return [1];
                                }
                                return $state;
                            })
                            ->columnSpanFull()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')
                                    ->columnSpanFull()
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->columnSpanFull()
                                    ->label('Slug')
                                    ->required()
                                    ->unique('categories', 'slug', ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull()
                                    ->required()
                                    ->label('Descripción')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columns(['default' => 1, 'md' => 2]),

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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    Tables\Actions\Action::make('publish_social')
                        ->label('Publicar')
                        ->icon('heroicon-o-share')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Confirmar publicación en redes sociales')
                        ->modalDescription(function (Content $record) {
                            $lastPublished = $record->last_social_published
                                ? $record->last_social_published->diffForHumans()
                                : 'Nunca';

                            return "¿Estás seguro de que quieres publicar este contenido en redes sociales?\n\nÚltima publicación: {$lastPublished}";
                        })
                        ->modalSubmitActionLabel('Publicar')
                        ->action(function (Content $record) {
                            try {
                                PublishToSocialHelper::publishContent($record);

                                Notification::make()
                                    ->title('Contenido publicado')
                                    ->body('El contenido se ha publicado exitosamente en redes sociales.')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error al publicar')
                                    ->body('Error: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(function (Content $record) {
                            ## Solo muestro contenido que no es para adultos
                            return !in_array($record->group_id, [4, 14]) && !$record->is_adult;
                        }),
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
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Content::whereNull('deleted_at')->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
            //'view' => Pages\ViewContent::route('/{record}'),
        ];
    }
}
