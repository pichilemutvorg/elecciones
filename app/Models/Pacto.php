<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $letter
 * @property string $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PactoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pacto whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'letter', 'icon',
    ];

    protected $casts = [

    ];
}
