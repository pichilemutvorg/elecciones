<?php

namespace App\Models;

use Database\Factories\LocalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Mesa> $mesas
 * @property-read int|null $mesas_count
 * @method static LocalFactory factory($count = null, $state = [])
 * @method static Builder|Local newModelQuery()
 * @method static Builder|Local newQuery()
 * @method static Builder|Local query()
 * @method static Builder|Local whereAddress($value)
 * @method static Builder|Local whereCreatedAt($value)
 * @method static Builder|Local whereId($value)
 * @method static Builder|Local whereName($value)
 * @method static Builder|Local whereUpdatedAt($value)
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
