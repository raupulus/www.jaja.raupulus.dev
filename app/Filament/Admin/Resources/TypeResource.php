<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\TypeResource\Pages;
use App\Filament\Admin\Resources\TypeResource\RelationManagers;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?string $navigationLabel = 'Tipos';

    protected static ?string $label = 'Tipo';

    protected static ?string $navigationGroup = 'Contenidos';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->columnSpanFull()
                    ->image()
                    ->disk('public')
                    ->directory('type-images')
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

                                //$originalName = $state->getClientOriginalName();

                                $converter = new ConvertImageToWebp();
                                $newPath = $converter($tempFile);

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
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->columnSpanFull()
                    ->label('Slug')
                    ->required()
                    ->unique('types', 'slug', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->label('Descripción')
                    ->columnSpanFull()
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
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
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                //Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            //'view' => Pages\ViewType::route('/{record}'),
            'edit' => Pages\EditType::route('/{record}/edit'),
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
