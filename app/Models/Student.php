<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Student extends Model
{
    use HasFactory;

    // Fields allowed for mass assignment
    protected $fillable = [
        'name',
        'email',
        'birthdate',
        'nationality',
    ];

    /**
     * Relación con la entidad Enrollment
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope para buscar estudiantes por name del estudiante
     */
    #[Scope]
    public function name(Builder $query, ?string $string): void
    {
        if (trim($string)!='')
            $query->where('name', 'ILIKE', "%$string%");
    }

    /**
     * Booted method for model events
     */
    protected static function booted(): void
    {
        // Before deleting an enrollment
        static::deleting(function ($model) {
            if( $model->enrollments()->count() > 0 )
                throw ValidationException::withMessages([
                    'error' => ['No se puede borrar la entidad ya que tiene otras entidades relacionadas.']
                ]);
        });
    }
}
