<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mesa> $mesas
 * @property-read int|null $mesas_count
 * @method static \Database\Factories\LocalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Local newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Local newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Local query()
 * @method static \Illuminate\Database\Eloquent\Builder|Local whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Local whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Local whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Local whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Local whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address',
    ];

    protected $casts = [

    ];

    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class);
    }
}
