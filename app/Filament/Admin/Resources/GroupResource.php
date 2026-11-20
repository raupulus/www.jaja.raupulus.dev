<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\RelationManagers;
use App\Filament\Support\WebpImageUpload;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
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
                WebpImageUpload::make('image', 'group-images')
                    ->columnSpanFull()
                    ->imageResizeTargetHeight(768)
                    ->imageResizeTargetWidth(1024)
                    ->imageResizeMode('crop'),

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
                    ->unique(
                        'groups',
                        'slug',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    ->maxLength(255),

                TextArea::make('description')
                    ->columnSpanFull()
                    ->label('Descripción')
                    ->maxLength(255)
                ,

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                Tables\Columns\TextColumn::make('contents_count')
                    ->label('Contenidos')
                    ->getStateUsing(function ($record) {
                        return $record->contents()->whereNull('deleted_at')->count();
                    })
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === 0 => 'gray',
                        $state <= 5 => 'warning',
                        $state <= 20 => 'success',
                        default => 'primary'
                    })
                    ->icon('heroicon-o-document-text')
                    ->sortable(false),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(50)
                    ->wrap()
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
                Tables\Filters\SelectFilter::make('type_id')
                    ->label('Tipo')
                    ->relationship('type', 'name')
                    ->preload(),
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
