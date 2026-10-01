<?php

namespace App\Models;

use Database\Factories\PactoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $letter
 * @property string $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static PactoFactory factory($count = null, $state = [])
 * @method static Builder|Pacto newModelQuery()
 * @method static Builder|Pacto newQuery()
 * @method static Builder|Pacto query()
 * @method static Builder|Pacto whereCreatedAt($value)
 * @method static Builder|Pacto whereIcon($value)
 * @method static Builder|Pacto whereId($value)
 * @method static Builder|Pacto whereLetter($value)
 * @method static Builder|Pacto whereName($value)
 * @method static Builder|Pacto whereUpdatedAt($value)
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
