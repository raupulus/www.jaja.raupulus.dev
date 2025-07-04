<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationLabel = 'Grupos';

    protected static ?string $label = 'Grupo';
    protected static ?string $navigationGroup = 'Contenidos';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('group-images')
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
                                $newPath = $converter($tempFile, 'group-images');

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
                    ->required()
                    ->label('Título')
                    ->maxLength(255),

                Forms\Components\Select::make('type_id')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Tipo')
                    ->placeholder('Seleccione un tipo')

                    // No me interesa crear tipos
                    /*
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nombre'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1024)
                            ->label('Descripción')

                    ])
                    */
                ,

                Forms\Components\TextInput::make('slug')
                    ->columnSpanFull()
                    ->label('Slug')
                    ->required()
                    ->unique('groups', 'slug', ignoreRecord: true)
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('contents_count')
                    ->label('Contenidos')
                    ->getStateUsing(function ($record) {
                        return $record->contents()->whereNull('deleted_at')->count();
                    })
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 20 => 'success',
                        default => 'primary'
                    })
                    ->icon('heroicon-o-document-text')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ContentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
