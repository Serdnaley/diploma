<?php
/**
 * Created by PhpStorm.
 * User: Andrew Pavluk (pavlukpro)
 * Date: 2020-02-03
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class TelegramBotService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TelegramBot';
    }
}
