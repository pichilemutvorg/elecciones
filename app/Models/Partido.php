<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $abbr
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PartidoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Partido newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partido newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partido query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partido whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'abbr', 'icon',
    ];

    protected $casts = [

    ];
}
