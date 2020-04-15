<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\TelegramChat
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $type
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $phone
 * @property object|null $action
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Car $car
 * @property-read mixed $name
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\TelegramChat onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TelegramChat withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\TelegramChat withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $invite_link
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TelegramChat whereInviteLink($value)
 * @property-read mixed $app_invite_link
 */
class TelegramChat extends Model
{
    use SoftDeletes;

    public $incrementing = false;


    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'type',
        'first_name',
        'last_name',
        'username',
        'phone',
        'action',
        'invite_link',
    ];

    protected $casts = [
        'action' => 'object'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function car()
    {
        return $this->hasOne(Car::class);
    }

    public function getNameAttribute() {

        if ( $this->title ) {
            $name = $this->title;
        } else {
            $name = $this->first_name;

            if ( $this->last_name ) {
                $name .= ' ' . $this->last_name;
            }
        }

        if ( $this->username ) {
            $name .= ' (@' . $this->username . ')';
        }

        if ( $this->phone ) {
            $name .= ' [+' . $this->phone . ']';
        }

        if ( $this->type !== 'private' ) {
            $name .= ' (' . $this->type . ')';
        }

        return $name;
    }


    /**
     * Создает новую ссылку для вступления в чат
     *
     * @return mixed
     */
    public function createInviteLink() {
        try {
            $response = \TelegramAPI::exportChatInviteLink([
                'chat_id' => $this->id
            ]);

            $this->invite_link = $response['result'];
            $this->save();

            return $this->invite_link;
        } catch ( \Exception $e ) {
            return false;
        }
    }

    /**
     * Ссылка для вступления в группу напрямую в приложении
     *
     * @return mixed
     */
    public function getAppInviteLinkAttribute() {
        return str_replace('https://t.me/joinchat/', 'tg://join?invite=', $this->invite_link);
    }
}
