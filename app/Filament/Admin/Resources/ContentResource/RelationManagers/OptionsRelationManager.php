<?php

namespace App\Filament\Admin\Resources\ContentResource\RelationManagers;

use App\Models\Content;
use App\Models\ContentOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $recordTitleAttribute = 'value';

    protected static ?string $title = 'Opciones';

    protected static ?string $label = 'Opción';

    protected static ?string $pluralLabel = 'Opciones';

    protected static ?string $modelLabel = 'Opción';

    protected static ?string $pluralModelLabel = 'Opciones';

    protected static ?string $icon = 'heroicon-o-check-circle';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Content && $ownerRecord->group?->type?->slug === 'quiz';
    }

    private function ownerContent(): Content
    {
        $owner = $this->getOwnerRecord();

        if (! $owner instanceof Content) {
            throw new \RuntimeException('Expected Content owner record.');
        }

        return $owner;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('value')
                    ->label('Valor')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('is_correct')
                    ->label('Correcta'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width('50px'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Valor')
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn ($state) => $state)
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('is_correct')
                    ->label('Correcta')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $owner = $this->ownerContent();

                        // # Al crear asigna el siguiente orden secuencial automáticamente
                        $maxOrder = $owner
                            ->options()
                            ->max('order') ?? 0;
                        $data['order'] = $maxOrder + 1;

                        // # En caso de ser marcada como correcta, desmarcar las demás
                        if ($data['is_correct'] ?? false) {
                            $owner
                                ->options()
                                ->update(['is_correct' => false]);
                        }

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, Model $record): array {
                        if (! $record instanceof ContentOption) {
                            return $data;
                        }

                        // # En caso de ser marcada como correcta, desmarcar las demás (excluyo la actual)
                        if (($data['is_correct'] ?? false) && ! $record->is_correct) {
                            $this->ownerContent()
                                ->options()
                                ->where('id', '!=', $record->id)
                                ->update(['is_correct' => false]);
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        // # Reajusto el orden después de eliminar
                        $options = $this->ownerContent()
                            ->options()
                            ->orderBy('order')
                            ->get();

                        foreach ($options as $index => $option) {
                            $option->update(['order' => $index + 1]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // # Reajusto el orden después de eliminar por lote
                            $options = $this->ownerContent()
                                ->options()
                                ->orderBy('order')
                                ->get();

                            foreach ($options as $index => $option) {
                                $option->update(['order' => $index + 1]);
                            }
                        }),
                ]),
            ]);
    }
}
