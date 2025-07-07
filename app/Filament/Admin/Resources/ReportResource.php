<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $label = 'Reporte';

    protected static ?string $navigationGroup = 'Moderación';

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Reporter')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Usuario Registrado'),

                        Forms\Components\TextInput::make('reporter_name')
                            ->label('Nombre del Reporter')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('reporter_email')
                            ->label('Email del Reporter')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('reporter_ip')
                            ->label('Dirección IP')
                            ->maxLength(45)
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contenido Reportado')
                    ->schema([
                        Forms\Components\TextInput::make('reportable_type')
                            ->label('Tipo de Contenido')
                            ->disabled(),

                        Forms\Components\TextInput::make('reportable_id')
                            ->label('ID del Contenido')
                            ->disabled(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Reporte')
                            ->options(Report::getTypes())
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options(Report::getPriorities())
                            ->required()
                            ->default('medium'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalles del Reporte')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(4),

                        Forms\Components\Textarea::make('additional_info')
                            ->label('Información Adicional')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Gestión Administrativa')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(Report::getStatuses())
                            ->required()
                            ->default('pending'),

                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Asignado a'),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notas del Administrador')
                            ->rows(4),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Fecha de Resolución')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reporter_name')
                    ->label('Reporter')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reportable_type')
                    ->label('Tipo de Contenido')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de Reporte')
                    ->formatStateUsing(fn (string $state): string => Report::getTypes()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'spam' => 'warning',
                        'inappropriate_content' => 'danger',
                        'adult_content' => 'danger',
                        'hate_speech' => 'danger',
                        'harassment' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->formatStateUsing(fn (string $state): string => Report::getPriorities()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'primary',
                        'low' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => Report::getStatuses()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewing' => 'primary',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Asignado a')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Fecha de Resolución')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Report::getStatuses()),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Reporte')
                    ->options(Report::getTypes()),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(Report::getPriorities()),

                Tables\Filters\SelectFilter::make('reportable_type')
                    ->label('Tipo de Contenido')
                    ->options([
                        'App\Models\Content' => 'Contenido',
                        'App\Models\Suggestion' => 'Sugerencia',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('mark_resolved')
                    ->label('Marcar como Resuelto')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Report $record): bool => $record->status !== 'resolved')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Notas del Administrador')
                            ->required(),
                    ])
                    ->action(function (array $data, Report $record): void {
                        $record->markAsResolved($data['admin_notes']);
                    }),

                Tables\Actions\Action::make('mark_rejected')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Report $record): bool => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Razón del Rechazo')
                            ->required(),
                    ])
                    ->action(function (array $data, Report $record): void {
                        $record->markAsRejected($data['admin_notes']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return Report::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = Report::where('status', 'pending')->count();
        $criticalCount = Report::where('priority', 'critical')->where('status', 'pending')->count();

        if ($criticalCount > 0) {
            return 'danger';
        }

        if ($pendingCount > 0) {
            return 'warning';
        }

        return null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            //'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
