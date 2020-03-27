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
