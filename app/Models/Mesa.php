<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $number
 * @property int $local_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Local|null $local
 * @method static \Database\Factories\MesaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mesa whereNumber($value)
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
