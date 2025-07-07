<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reporter_name',
        'reporter_email',
        'reporter_ip',
        'reportable_type',
        'reportable_id',
        'type',
        'title',
        'description',
        'additional_info',
        'status',
        'priority',
        'assigned_to',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Contenido reportado (polimórfico)
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que hizo el reporte
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Administrador asignado al reporte
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /**
     * Accessors
     */
    public function getReporterNameAttribute($value)
    {
        return $value ?? ($this->user ? $this->user->name : 'Anónimo');
    }

    public function getReporterEmailAttribute($value)
    {
        return $value ?? ($this->user ? $this->user->email : 'No disponible');
    }

    /**
     * Métodos de utilidad
     */
    public function markAsResolved($adminNotes = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
    }

    public function markAsRejected($adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'resolved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
    }

    /**
     * Constantes para tipos de reporte
     */
    public static function getTypes(): array
    {
        return [
            'spam' => 'Spam',
            'inappropriate_content' => 'Contenido Inapropiado',
            'adult_content' => 'Contenido Adulto',
            'hate_speech' => 'Discurso de Odio',
            'copyright' => 'Violación de Derechos de Autor',
            'violence' => 'Violencia',
            'harassment' => 'Acoso',
            'misinformation' => 'Desinformación',
            'other' => 'Otro',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pendiente',
            'reviewing' => 'En Revisión',
            'resolved' => 'Resuelto',
            'rejected' => 'Rechazado',
            'closed' => 'Cerrado',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'critical' => 'Crítica',
        ];
    }
}
