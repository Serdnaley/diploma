<?php
/**
 * Created by PhpStorm.
 * User: Andrew Pavluk (pavlukpro)
 * Date: 2020-02-16
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class TelegramAPIService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TelegramAPI';
    }
}
