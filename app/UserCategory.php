<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserCategory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserCategory whereUpdatedAt($value)
 */
class UserCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }
}
