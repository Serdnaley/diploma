<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Report
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserFile[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report query()
 * @mixin \Eloquent
 */
class Report extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'type',
    ];

    protected $dates = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(UserFile::class);
    }
}
