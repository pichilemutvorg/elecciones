<?php

namespace App\Models;

use Database\Factories\ConcejalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $number
 * @property int|null $is_independent
 * @property string $name
 * @property string|null $color
 * @property string|null $photo
 * @property int|null $partido_id
 * @property int|null $pacto_id
 * @property int|null $subpacto_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Pacto|null $pacto
 * @property-read Partido|null $partido
 * @property-read Subpacto|null $subpacto
 * @property-read Collection<int, ResultadosConcejal> $votacion
 * @property-read int|null $votacion_count
 * @method static ConcejalFactory factory($count = null, $state = [])
 * @method static Builder<static>|Concejal newModelQuery()
 * @method static Builder<static>|Concejal newQuery()
 * @method static Builder<static>|Concejal query()
 * @method static Builder<static>|Concejal whereColor($value)
 * @method static Builder<static>|Concejal whereCreatedAt($value)
 * @method static Builder<static>|Concejal whereId($value)
 * @method static Builder<static>|Concejal whereIsIndependent($value)
 * @method static Builder<static>|Concejal whereName($value)
 * @method static Builder<static>|Concejal whereNumber($value)
 * @method static Builder<static>|Concejal wherePactoId($value)
 * @method static Builder<static>|Concejal wherePartidoId($value)
 * @method static Builder<static>|Concejal wherePhoto($value)
 * @method static Builder<static>|Concejal whereSubpactoId($value)
 * @method static Builder<static>|Concejal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Concejal extends Model
{
    use HasFactory;

    protected $table = 'concejales';

    protected $fillable = [
        'number',
        'is_independent',
        'name',
        'color',
        'photo',
        'partido_id',
        'pacto_id',
        'subpacto_id',
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

    public function subpacto(): BelongsTo
    {
        return $this->belongsTo(Subpacto::class, 'subpacto_id');
    }

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }

    public function votacion()
    {
        return $this->hasMany(ResultadosConcejal::class, 'concejal_id');
    }
}
