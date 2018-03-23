<?php
/**
 * Created by PhpStorm.
 * User: cleyer
 * Date: 31/10/2017
 * Time: 11:31
 */

namespace App\Controller;

use FOS\RestBundle\Exception\InvalidParameterException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExceptionController extends Controller
{
    public function exceptionAction(\Exception $exception)
    {
//        dump($exception);exit;
        $code = ($exception->getCode()) ? $exception->getCode() : 666;
        if ($exception instanceof InvalidParameterException) {
            $code = 999;
        }

        $result = array(
            'timestamp' => date('c'),
            'trackingId' => $this->get('qubit.utilities.tracking_code')->getTrackingCode(),
            'code' => $code,
            'message' => $exception->getMessage()
        );

        if ($this->get('kernel')->getEnvironment() === "dev") {
            $result['file'] = $exception->getFile();
            $result['line'] = $exception->getLine();
        }
        return $this->json(
            $result,
            method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500
        );
    }

}