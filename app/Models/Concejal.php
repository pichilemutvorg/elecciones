<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $number
 * @property int|null $is_independent
 * @property string $name
 * @property string|null $color
 * @property string|null $photo
 * @property int|null $partido_id
 * @property int|null $pacto_id
 * @property int|null $subpacto_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pacto|null $pacto
 * @property-read \App\Models\Partido|null $partido
 * @property-read \App\Models\Subpacto|null $subpacto
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResultadosConcejal> $votacion
 * @property-read int|null $votacion_count
 * @method static \Database\Factories\ConcejalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereIsIndependent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal wherePactoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal wherePartidoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereSubpactoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Concejal whereUpdatedAt($value)
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
