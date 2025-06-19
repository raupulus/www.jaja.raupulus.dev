<?php

namespace App\Filament\Panel\Resources\CollaboratorResource\RelationManagers;

use App\Actions\ConvertImageToWebp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $navigationLabel = 'Mis Proyectos';

    protected static ?string $label = 'Proyecto';

    protected static ?string $pluralLabel = 'Proyectos';

    protected static ?string $modelLabel = 'Proyecto';

    protected static ?string $pluralModelLabel = 'Proyectos';

    protected static ?string $title = 'Mis Proyectos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->columnSpanFull()
                    ->image()
                    ->disk('public')
                    ->directory('collaborator_projects-images')
                    ->visibility('public')
                    ->imageEditor()
                    ->label('Imagen')
                    ->default(null)
                    ->imageResizeTargetHeight(500)
                    ->imageResizeTargetWidth(800)
                    ->imageResizeMode('crop')
                    ->afterStateUpdated(function (Forms\Components\FileUpload $component, $state) {
                        if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            try {
                                $tempFile = $state->getRealPath();
                                $converter = new ConvertImageToWebp();
                                $newPath = $converter($tempFile, 'collaborator_projects-images');

                                if ($newPath && $newPath !== $tempFile) {
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
                    ->unique('collaborator_projects', 'slug', ignoreRecord: true)
                    ->rules(['alpha_dash']),
                Forms\Components\TextInput::make('url')
                    ->label('Url')
                    ->maxLength(255)
                    ->rules(['url']),
                Forms\Components\Textarea::make('excerpt')
                    ->label('Resumen')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\MarkdownEditor::make('content')
                    ->columnSpanFull()
                    ->label('Contenido')
                    ->required()
                    ->maxLength(10240)
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
                Forms\Components\Select::make('type')
                    ->required()
                    ->label('Tipo')
                    ->options([
                        'web' => 'Web',
                        'mobile' => 'Mobile',
                        'desktop' => 'Desktop',
                        'bot' => 'Bot',
                        'marketing' => 'Marketing',
                        'other' => 'Otro',
                    ])
                    ->default('other'),

                Forms\Components\Select::make('repository_type')
                    ->label('Tipo de repositorio')
                    ->required()
                    ->options([
                        'github' => 'Github',
                        'gitlab' => 'Gitlab',
                        'bitbucket' => 'Bitbucket',
                        'other' => 'Otro',
                    ])
                    ->default('github'),
                /*
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->required()
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ])
                    ->default('draft')
                    ->native(false),
                */
                Forms\Components\TagsInput::make('keywords')
                    ->columnSpanFull()
                    ->label('Palabras Clave')
                    ->helperText('Presiona Enter para agregar una palabra clave')
                    ->separator(',')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Imagen'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'web' => 'success',
                        'mobile' => 'warning',
                        'desktop' => 'info',
                        'bot' => 'gray',
                        'marketing' => 'danger',
                        'other' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Resumen')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                    })
                    ->sortable(),
                /*
                Tables\Columns\SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'published' => 'Publicado',
                        'draft' => 'Borrador',
                    ])
                    ->sortable(),
                */
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'web' => 'Web',
                        'mobile' => 'Mobile',
                        'desktop' => 'Desktop',
                        'bot' => 'Bot',
                        'marketing' => 'Marketing',
                        'other' => 'Otro',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuevo Proyecto'),
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No tienes proyectos')
            ->emptyStateDescription('Crea tu primer proyecto para comenzar')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear proyecto')
                    ->icon('heroicon-o-plus'),
            ]);
    }
}
