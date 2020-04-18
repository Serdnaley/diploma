<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UserFile
 *
 * @property-read mixed $url
 * @property-read \App\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile query()
 * @mixin \Eloquent
 * @property int $id
 * @property int|null $owner_id
 * @property string $name
 * @property string $original_name
 * @property string $path
 * @property string $type
 * @property string|null $report_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserFile whereUpdatedAt($value)
 */
class UserFile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'original_name',
        'name',
        'path',
        'type',
        'report_id',
        'created_by',
    ];

    /**
     * Добавляем URL в ответ
     *
     * @var array
     */
    protected $appends = [
        'url'
    ];

    /**
     * Диск для хранения файлов
     *
     * @var string
     */
    static $disk = 'public';


    /**
     * Директория хранения файлов
     *
     * @var string
     */
    static $upload_dir = 'user_files';


    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Сохранение файла на сервере
     *
     * @param        $file
     * @param string $category
     *
     * @return UserFile|Model
     */
    public static function store_file($file)
    {
        $dir_path = self::$upload_dir . '/' . Carbon::now()->format('Y-m');

        $path = \Storage::disk( self::$disk )->putFile($dir_path, $file);

        $user_file = UserFile::create([
            'original_name' => $file->getClientOriginalName(),
            'name' => $file->hashName(),
            'path' => $path,
            'type' => $file->getMimeType(),
            'owner_id' => \Auth::id(),
        ]);

        return $user_file;
    }


    public function getUrlAttribute()
    {
        return \Storage::disk( self::$disk )->url($this->path);
    }
}
