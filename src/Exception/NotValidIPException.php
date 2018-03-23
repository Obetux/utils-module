<?php

namespace App\Exception;

/**
 * Created by PhpStorm.
 * User: cleyer
 * Date: 31/10/2017
 * Time: 11:33
 */
class NotValidIPException extends CustomException
{
    protected $code = 4010;
    protected $message = "Not valid IP Address";
    protected $statusCode = "500";

}