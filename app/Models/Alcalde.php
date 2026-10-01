<?php

namespace App\Models;

use Database\Factories\AlcaldeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $number
 * @property int $is_independent
 * @property string $name
 * @property int|null $partido_id
 * @property int|null $pacto_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Pacto|null $pacto
 * @property-read Partido|null $partido
 * @method static AlcaldeFactory factory($count = null, $state = [])
 * @method static Builder|Alcalde newModelQuery()
 * @method static Builder|Alcalde newQuery()
 * @method static Builder|Alcalde query()
 * @method static Builder|Alcalde whereCreatedAt($value)
 * @method static Builder|Alcalde whereId($value)
 * @method static Builder|Alcalde whereIsIndependent($value)
 * @method static Builder|Alcalde whereName($value)
 * @method static Builder|Alcalde whereNumber($value)
 * @method static Builder|Alcalde wherePactoId($value)
 * @method static Builder|Alcalde wherePartidoId($value)
 * @method static Builder|Alcalde whereUpdatedAt($value)
 * @property-read Collection<int, ResultadosAlcalde> $votacion
 * @property-read int|null $votacion_count
 * @property string|null $color
 * @property string|null $photo
 * @method static Builder<static>|Alcalde whereColor($value)
 * @method static Builder<static>|Alcalde wherePhoto($value)
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
