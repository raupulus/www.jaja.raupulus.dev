<?php

namespace App\Filament\Admin\Resources;

use App\Actions\ConvertImageToWebp;
use App\Filament\Admin\Resources\PageResource\Pages;
use App\Filament\Admin\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationLabel = 'Páginas';

    protected static ?string $label = 'Página';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make('Información Principal')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Título')
                                ->required()
                                ->columnSpanFull()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                                    if ($operation !== 'create') {
                                        return;
                                    }
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(Page::class, 'slug', ignoreRecord: true)
                                ->rules(['alpha_dash'])
                                ->helperText('URL amigable para la página'),

                            Forms\Components\Select::make('status')
                                ->label('Estado')
                                ->required()
                                ->options([
                                    'draft' => 'Borrador',
                                    'published' => 'Publicado',
                                ])
                                ->default('draft')
                                ->native(false),

                            Forms\Components\Textarea::make('excerpt')
                                ->label('Extracto/Descripción')
                                ->rows(3)
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->helperText('Breve descripción de la página'),


                        ])
                        ->columns(['default' => 1, 'md' => 2]),

                    Forms\Components\Section::make('Contenido')
                        ->schema([
                            Forms\Components\MarkdownEditor::make('content')
                                ->label('Contenido')
                                ->toolbarButtons([
                                    'attachFiles',
                                    'blockquote',
                                    'bold',
                                    'bulletList',
                                    'codeBlock',
                                    'heading',
                                    'italic',
                                    'link',
                                    'orderedList',
                                    'redo',
                                    'strike',
                                    'table',
                                    'undo',
                                ])
                                ->columnSpanFull()
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            if (preg_match('/^# /m', $value) || preg_match('/<h1[^>]*>/i', $value)) {
                                                $fail('El contenido no puede contener títulos H1. Usa H2 (##) o inferiores.');
                                            }
                                        };
                                    },
                                ])
                                ->helperText('💡 Usa H2 (##) como título principal, H3 (###) para subtítulos, etc. No se permiten H1 (#).')
                            ,
                        ]),
                ])
                    ->columnSpan(['default' => 1, 'lg' => 2]),

                Forms\Components\Group::make([
                    Forms\Components\Section::make('Imagen')
                        ->schema([
                            Forms\Components\FileUpload::make('image')
                                ->columnSpanFull()
                                ->image()
                                ->disk('public')
                                ->directory('page-images')
                                ->visibility('public')
                                ->imageEditor()
                                ->label('Imagen')
                                ->default(null)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(2048)
                                ->imageResizeTargetHeight(600)
                                ->imageResizeTargetWidth(1024)
                                ->imageResizeMode('crop', )
                                ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                                    if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                        try {
                                            $tempFile = $state->getRealPath();
                                            $converter = new ConvertImageToWebp();
                                            $newPath = $converter($tempFile, 'page-images');

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

                            Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TagsInput::make('keywords')
                                ->label('Palabras Clave')
                                ->helperText('Presiona Enter para agregar una palabra clave')
                                ->separator(',')
                                ,
                            ])
                        ]),

                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ])->columns(['default' => 1, 'lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                Tables\Columns\TextColumn::make('title')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->sortable()
                    ->searchable()
                    ->label('Título'),
                Tables\Columns\TextColumn::make('excerpt')
                    ->limit(60)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->sortable()
                    ->searchable()
                    ->label('Descripción'),
                Tables\Columns\TextColumn::make('keywords')
                    ->label('Palabras Clave')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\SelectColumn::make('status')->label('Estado')->options([
                    'published' => 'Publicado',
                    'draft' => 'Borrador',
                ]),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'published' => 'Publicado',
                        'draft' => 'Borrador',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
