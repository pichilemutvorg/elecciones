<?php

namespace App\Models;

use Database\Factories\ResultadosAlcaldeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $mesa_id
 * @property int $alcalde_id
 * @property int $votes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Alcalde|null $alcalde
 * @property-read Mesa|null $mesa
 * @method static ResultadosAlcaldeFactory factory($count = null, $state = [])
 * @method static Builder|ResultadosAlcalde newModelQuery()
 * @method static Builder|ResultadosAlcalde newQuery()
 * @method static Builder|ResultadosAlcalde query()
 * @method static Builder|ResultadosAlcalde whereAlcaldeId($value)
 * @method static Builder|ResultadosAlcalde whereCreatedAt($value)
 * @method static Builder|ResultadosAlcalde whereId($value)
 * @method static Builder|ResultadosAlcalde whereMesaId($value)
 * @method static Builder|ResultadosAlcalde whereUpdatedAt($value)
 * @method static Builder|ResultadosAlcalde whereVotes($value)
 * @mixin \Eloquent
 */
class ResultadosAlcalde extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesa_id',
        'alcalde_id',
        'votes',
    ];

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function alcalde(): BelongsTo
    {
        return $this->belongsTo(Alcalde::class, 'alcalde_id');
    }
}
