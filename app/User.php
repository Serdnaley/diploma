<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $patronymic
 * @property string $role
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePatronymic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\UserCategory $category
 * @property int|null $user_category_id
 * @property int|null $telegram_chat_id
 * @property string $fast_auth_token
 * @property-read mixed $auth_url
 * @property-read string $full_name
 * @property-read string $short_name
 * @property-read \App\TelegramChat|null $telegram_chat
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFastAuthToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTelegramChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUserCategoryId($value)
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'user_category_id',
        'role',
        'email',
//        'password',

        'telegram_chat_id',
        'fast_auth_token',
    ];

    /**
     * All available roles
     *
     * @var array
     */
    protected $roles = [
        'admin',
        'user',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'short_name',
        'auth_url',
    ];

    public function category()
    {
        return $this->belongsTo(UserCategory::class);
    }

    public function telegram_chat()
    {
        return $this->belongsTo(TelegramChat::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Проверяет есть ли у пользователя роль из переданных
     *
     * @param $roles string|array Roles
     *
     * @return bool
     */
    public function has_role($roles)
    {
        $roles = (array)$roles;

        foreach ($roles as $role) {
            if ($role == $this->role) return true;
        }

        return false;
    }

    /**
     * User full name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [
            $this->last_name,
            $this->first_name,
            $this->patronymic
        ]);
    }

    /**
     * User full name
     *
     * @return string
     */
    public function getShortNameAttribute()
    {

        $name = $this->last_name;
        $name .= ' ' . mb_strtoupper(mb_substr($this->first_name, 0, 1)) . '.';
        $name .= ' ' . mb_strtoupper(mb_substr($this->patronymic, 0, 1)) . '.';

        return $name;
    }


    /**
     * User auth url
     *
     * @return string
     */
    public function getAuthUrlAttribute()
    {
        $path = '/login';
        $attributes = [
            'auth_token' => $this->fast_auth_token
        ];

        $url = config('app.url');

        if ($path) {
            $url = trim($url, '/') . '/' . trim($path, '/');
        }

        if ($attributes) {
            $url .= '?' . http_build_query($attributes);
        }

        return $url;
    }

    /**
     * User fast auth token
     *
     * @return string
     */
    public function getFastAuthTokenAttribute($value)
    {
        if (!$value) {
            $this->makeFastAuthToken();
        }

        return $this->attributes['fast_auth_token'];
    }


    /**
     * Генерируем новый токен быстрой авторизации для юзера
     *
     * @return bool
     */
    public function makeFastAuthToken()
    {

        $this->fast_auth_token = null;

        for ($i = 0; $i <= 100; $i++) {
            $token = Str::random(20);

            if (!User::where(['fast_auth_token' => $token])->first()) {
                $this->fast_auth_token = $token;
                break;
            }

        }

        $this->save();

        return false;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
