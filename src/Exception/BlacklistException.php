<?php

namespace App\Exception;

/**
 * Class BlacklistException
 * @package App\Exception
 */
class BlacklistException extends CustomException
{
    protected $code = 4011;
    protected $message = "IP Address in Blacklist";
    protected $statusCode = "500";

}