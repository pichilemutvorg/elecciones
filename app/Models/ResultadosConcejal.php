<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal query()
 * @property int $id
 * @property int $mesa_id
 * @property int $concejal_id
 * @property int $votes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Concejal|null $concejal
 * @property-read \App\Models\Mesa|null $mesa
 * @method static \Database\Factories\ResultadosConcejalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereConcejalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereMesaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadosConcejal whereVotes($value)
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
