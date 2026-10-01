<?php

namespace App\Models;

use Database\Factories\ResultadosConcejalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @method static Builder<static>|ResultadosConcejal newModelQuery()
 * @method static Builder<static>|ResultadosConcejal newQuery()
 * @method static Builder<static>|ResultadosConcejal query()
 * @property int $id
 * @property int $mesa_id
 * @property int $concejal_id
 * @property int $votes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Concejal|null $concejal
 * @property-read Mesa|null $mesa
 * @method static ResultadosConcejalFactory factory($count = null, $state = [])
 * @method static Builder<static>|ResultadosConcejal whereConcejalId($value)
 * @method static Builder<static>|ResultadosConcejal whereCreatedAt($value)
 * @method static Builder<static>|ResultadosConcejal whereId($value)
 * @method static Builder<static>|ResultadosConcejal whereMesaId($value)
 * @method static Builder<static>|ResultadosConcejal whereUpdatedAt($value)
 * @method static Builder<static>|ResultadosConcejal whereVotes($value)
 * @mixin \Eloquent
 */
class ResultadosConcejal extends Model
{
    use HasFactory;

    protected $table = 'resultados_concejales';

    protected $fillable = [
        'mesa_id',
        'concejal_id',
        'votes',
    ];

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function concejal(): BelongsTo
    {
        return $this->belongsTo(Concejal::class, 'concejal_id');
    }
}
