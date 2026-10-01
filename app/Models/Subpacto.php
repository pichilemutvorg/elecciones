<?php

namespace App\Models;

use Database\Factories\SubpactoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static SubpactoFactory factory($count = null, $state = [])
 * @method static Builder|Subpacto newModelQuery()
 * @method static Builder|Subpacto newQuery()
 * @method static Builder|Subpacto query()
 * @method static Builder|Subpacto whereCreatedAt($value)
 * @method static Builder|Subpacto whereId($value)
 * @method static Builder|Subpacto whereName($value)
 * @method static Builder|Subpacto whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subpacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [

    ];
}
