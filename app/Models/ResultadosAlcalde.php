<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $mesa_id
 * @property int $alcalde_id
 * @property int $votes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Alcalde|null $alcalde
 * @property-read \App\Models\Mesa|null $mesa
 * @method static \Database\Factories\ResultadosAlcaldeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereAlcaldeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereMesaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultadosAlcalde whereVotes($value)
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
