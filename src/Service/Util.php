<?php
/**
 * Created by PhpStorm.
 * User: cleyer
 * Date: 05/12/2017
 * Time: 16:23
 */

namespace App\Service;

/**
 * Class Util
 * @package App\Service
 */
class Util
{

    /**
     *
     * Sanitiza las key para usar para Cache
     *
     * @param $key
     *
     * @return mixed
     */
    public static function sanitizeCacheKey($key)
    {
        $chars = ["{","}","(",")","/","\\","@",":"];
        $key = str_replace($chars, "", $key);

        return $key;
    }


}