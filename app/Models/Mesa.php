<?php

namespace App\Models;

use Database\Factories\MesaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $number
 * @property int $local_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Local|null $local
 * @method static MesaFactory factory($count = null, $state = [])
 * @method static Builder|Mesa newModelQuery()
 * @method static Builder|Mesa newQuery()
 * @method static Builder|Mesa query()
 * @method static Builder|Mesa whereCreatedAt($value)
 * @method static Builder|Mesa whereId($value)
 * @method static Builder|Mesa whereLocalId($value)
 * @method static Builder|Mesa whereName($value)
 * @method static Builder|Mesa whereUpdatedAt($value)
 * @method static Builder|Mesa whereNumber($value)
 * @mixin \Eloquent
 */
class Mesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'local_id',
    ];

    protected $casts = [

    ];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }
}
