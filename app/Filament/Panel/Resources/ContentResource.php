<?php

namespace App\Filament\Panel\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Panel\Resources\ContentResource\Pages;
use App\Filament\Panel\Resources\ContentResource\RelationManagers\OptionsRelationManager;
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
                Forms\Components\FileUpload::make('image')
                    ->columnSpanFull()
                    ->image()
                    ->disk('public')
                    ->directory('content-images')
                    ->visibility('public')
                    ->imageEditor()
                    ->label('Imagen')
                    ->default(null)
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
                    })
                ,
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(1024),

                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'title')
                    ->columnSpanFull()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Grupo')
                    ->placeholder('Seleccione un grupo')
                    ,

                Forms\Components\Select::make('category_id')
                    ->label('Categories')
                    ->multiple()
                    ->relationship('categories', 'title')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
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
                    ->limit(100)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Contenido')
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
