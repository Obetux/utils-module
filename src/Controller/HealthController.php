<?php

namespace App\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\AbstractCache;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Swagger\Annotations as SWG;

/**
 * Class HealthController
 * @package App\Controller
 *
 * @Route("/health")
 *
 */
class HealthController extends Controller
{
    /**
     * @Get("/ping")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Make a ping"
     * )
     * @SWG\Tag(name="Health")
     */
    public function ping()
    {
        return $this->json(['alive' => true]);
    }

    /**
     * @Get("/check/db")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Check connection to data base"
     * )
     * @SWG\Tag(name="Health")
     */
    public function checkDataBase()
    {
        $test = false;
        $manager = $this->getDoctrine()->getManager();
        try {
            $manager->getConnection()->connect();
            $test = true;
        } catch (\Exception $e) {
            // failed to connect
        }
        return $this->json(['alive' => $test]);
    }

    /**
     * @Get("/check/cache")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Check connection to cache"
     * )
     * @SWG\Tag(name="Health")
     * @param AbstractCache $geoIpCache
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkCache(AbstractCache $geoIpCache)
    {
        $test = true;

        try {
            $geoIpCache->set('test', 'test');
        } catch (\Exception $exception) {
            $test = false;
        }
        $stopwatch = $this->get('qubit.logger.stopwatch_wrapper');
        $time = $stopwatch->getDurationLap();
        return $this->json(['alive' => $test, 'lapse' => $time]);
    }
}
