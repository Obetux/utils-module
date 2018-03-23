<?php
/**
 * Created by PhpStorm.
 * User: Claudio
 * Date: 31/10/2017
 * Time: 23:58
 */

namespace App\Exception;


class CustomException extends \Exception
{
    protected $statusCode = "500";

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusCode
     * @return CustomException
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}