<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;
    // Fields allowed for mass assignment
    protected $fillable = [
        'student_id',
        'course_id',
        'enrolled_at',
    ];

    /**
     * Relación con la entidad Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relación con la entidad Course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope para buscar inscripciones por id del estudiante
     */
    #[Scope]
    public function estudianteId(Builder $query, ?int $id): void
    {
        if (trim($id)!='')
            $query->where('student_id', $id);
    }

    /**
     * Scope para buscar inscripciones por id del curso
     */
    #[Scope]
    public function cursoId(Builder $query, ?int $id): void
    {
        if (trim($id)!='')
            $query->where('course_id', $id);
    }
}
