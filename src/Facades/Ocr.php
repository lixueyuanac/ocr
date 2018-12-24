<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/24/024
 * Time: 10:21
 */

namespace Xueyuan\Ocr\Facades;

use Illuminate\Support\Facades\Facade;

class Ocr extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Xueyuan\Ocr\Ocr::class;
    }
}