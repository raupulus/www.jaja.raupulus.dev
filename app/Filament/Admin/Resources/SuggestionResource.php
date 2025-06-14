<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\SuggestionResource\Pages;
use App\Filament\Admin\Resources\SuggestionResource\RelationManagers;
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
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_path')
                    ->image()
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('suggestion-images')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageResizeTargetHeight(768)
                    ->imageResizeTargetWidth(1024)
                    //->imageEditorAspectRatios(['1:1'])
                    ->imageResizeMode('crop')
                    //->imageEditorMode(2)
                    ->label('Imagen')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                        if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            try {
                                $tempFile = $state->getRealPath();
                                //\Log::info("Ruta temporal del archivo: " . $tempFile);

                                $originalName = $state->getClientOriginalName();

                                $converter = new ConvertImageToWebp();
                                $newPath = $converter($tempFile, 'suggestion-images');

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

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->label('Contenido')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nick')
                    ->label('Nick')
                    ->maxLength(255),


                Forms\Components\Select::make('type_id')
                    ->label('Tipo')
                    ->searchable()
                    ->relationship('type', 'name')
                    ->required()
                    ->preload()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        ## Cuando cambia el tipo, reseteo el grupo
                        $set('group_id', null);
                    }),

                Forms\Components\Select::make('group_id')
                    ->label('Grupo')
                    ->searchable()
                    ->options(function (Forms\Get $get) {
                        $typeId = $get('type_id');

                        if (!$typeId) {
                            return [];
                        }

                        ## Grupos que pertenecen al tipo seleccionado
                        return Group::where('type_id', $typeId)
                            ->pluck('title', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->disabled(fn (Forms\Get $get) => !$get('type_id'))

                    /*
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
                            ->placeholder('Seleccione un tipo')

                    ])
                    */
                ,



                Forms\Components\Select::make('categories')
                    ->label('Categorías')
                    ->multiple()
                    ->relationship('categories', 'title')
                    ->preload()
                    ->searchable()
                    ->required()
                ,


                Forms\Components\TextInput::make('ip_address')
                    ->label('IP')
                    ->readOnly(),
                Forms\Components\TextInput::make('user_agent')
                    ->label('User Agent')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Imagen'),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nick')
                    ->label('Nick')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')->label('IP'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            //
        ];
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
