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
