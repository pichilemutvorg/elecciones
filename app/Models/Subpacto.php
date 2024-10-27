<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SubpactoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subpacto whereUpdatedAt($value)
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
