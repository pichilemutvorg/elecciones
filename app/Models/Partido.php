<?php

namespace App\Models;

use Database\Factories\PartidoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $abbr
 * @property string|null $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static PartidoFactory factory($count = null, $state = [])
 * @method static Builder|Partido newModelQuery()
 * @method static Builder|Partido newQuery()
 * @method static Builder|Partido query()
 * @method static Builder|Partido whereAbbr($value)
 * @method static Builder|Partido whereCreatedAt($value)
 * @method static Builder|Partido whereIcon($value)
 * @method static Builder|Partido whereId($value)
 * @method static Builder|Partido whereName($value)
 * @method static Builder|Partido whereUpdatedAt($value)
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
