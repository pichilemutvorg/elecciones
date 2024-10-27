<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $number
 * @property int $is_independent
 * @property string $name
 * @property int|null $partido_id
 * @property int|null $pacto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pacto|null $pacto
 * @property-read \App\Models\Partido|null $partido
 * @method static \Database\Factories\AlcaldeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde query()
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereIsIndependent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde wherePactoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde wherePartidoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alcalde whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResultadosAlcalde> $votacion
 * @property-read int|null $votacion_count
 * @property string|null $color
 * @property string|null $photo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alcalde whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alcalde wherePhoto($value)
 * @mixin \Eloquent
 */
class Alcalde extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'is_independent',
        'name',
        'color',
        'photo',
        'partido_id',
        'pacto_id',
    ];

    public function apellido(): string
    {
        $parts = explode(' ', $this->name);

        return $parts[1] ?? $this->name;
    }

    public function pacto(): BelongsTo
    {
        return $this->belongsTo(Pacto::class, 'pacto_id');
    }

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }

    public function votacion(): HasMany
    {
        return $this->hasMany(ResultadosAlcalde::class);
    }
}
