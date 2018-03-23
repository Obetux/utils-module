<?php

namespace App\Controller;

use Qubit\Bundle\UtilsBundle\Context\Context;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/v1/api/misc")
 *
 */
class MiscController extends Controller
{
    private $stopwatch;

    /**
     * MiscController constructor.
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch
     */
    public function __construct(\Symfony\Component\Stopwatch\Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }


    /**
     * @Post("/ehlo", name="ehlo")
     * @RequestParam(name="service", strict=true,  nullable=false, description="Service")
     * @RequestParam(name="ipAddress", strict=true,  nullable=false, description="Device IpAddress")
     * @RequestParam(name="userAgent", strict=true,  nullable=false, description="Device User Agent")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sarlanga"
     * )
     * @SWG\Tag(name="Misc")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return JsonResponse
     */
    public function ehlo(ParamFetcher $paramFetcher)
    {
//        $this->stopwatch = $this->get('debug.stopwatch');
        $this->stopwatch->start('GeoIpService::ehlo');

        $service =  $paramFetcher->get('service');
        $ipAddress = $paramFetcher->get('ipAddress');
        $userAgent = $paramFetcher->get('userAgent');

        $geoIp = $this->get('App\Service\GeoIpService')->ehlo($service, $ipAddress, $userAgent);

//TODO hacer bien todo esto
        if ($this->stopwatch) {
            $this->stopwatch->start('Context::initialize');
        }
        $context = Context::getInstance();
        $context->setService($service);
        $context->setVertical($service);
        $context->setIpAddress($ipAddress);
        $context->setUserAgent($userAgent);
        $context->setRegion($geoIp["region"]);
        if ($this->stopwatch) {
            $this->stopwatch->stop('Context::initialize');
        }

        $this->stopwatch->stop('GeoIpService::ehlo');

        return $this->json($geoIp);
    }
}
