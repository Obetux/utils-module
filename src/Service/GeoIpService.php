<?php
/**
 * Created by PhpStorm.
 * User: Claudio
 * Date: 30/12/2017
 * Time: 16:44
 */

namespace App\Service;

use App\Classes\Constant;
use App\Exception\NotValidIPException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bridge\Monolog\Logger;

class GeoIpService {
    /**
     * @var CacheInterface $cache
     */
    private $cache;

    /**
     * @var Logger $logger
     */
    private $logger;
    /**
     * @var
     */
    private $geoIp;

    /**
     * @var
     */
    private $blacklist;

    /**
     * @var
     */
    private $whitelist;

    /**
     * @var
     */
    private $stopwatch;

    /**
     * GeoIpService constructor.
     *
     * @param CacheInterface $cache
     * @param Logger $logger
     * @param \Cravler\MaxMindGeoIpBundle\Service\GeoIpService $geoIp
     * @param BlacklistService $blacklist
     * @param WhitelistService $whitelist
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch
     */
    public function __construct(
        CacheInterface $cache,
        Logger $logger,
        \Cravler\MaxMindGeoIpBundle\Service\GeoIpService $geoIp,
        \App\Service\BlacklistService $blacklist,
        \App\Service\WhitelistService $whitelist,
        \Symfony\Component\Stopwatch\Stopwatch $stopwatch
    ) {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->geoIp = $geoIp;
        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @param $service
     * @param $ipAddress
     *
     * @return array
     * @throws NotValidIPException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \App\Exception\BlacklistException
     */
    public function ehlo($service, $ipAddress, $userAgent)
    {
        $this->stopwatch->start('GeoIpService::ehlo');

        $this->logger->info('Ehlo attempt', ['service' => $service, 'ipAddress' => $ipAddress]);
        // Replace "city" with the appropriate method for your database, e.g., "country".

        $this->blacklist->check($ipAddress);

        if ($whitelist = $this->whitelist->check($ipAddress)) {
            $this->stopwatch->stop('GeoIpService::ehlo');
            return $whitelist;
        }
        $key = Util::sanitizeCacheKey('geo_ip.'.$ipAddress);
        if (!$this->cache->has($key)) {
            try {
                $this->stopwatch->start('Maxmind:getRecord');
                $record = $this->geoIp->getRecord($ipAddress, 'country');
                $this->stopwatch->stop('Maxmind:getRecord');
            } catch (\Exception $e) {
                $this->logger->warning('Not valid IP address', ['service' => $service, 'ipAddress' => $ipAddress]);
                if ($this->stopwatch) {
                    $this->stopwatch->stop('GeoIpService::ehlo');
                }
                throw new NotValidIPException();
            }

            $result = [
                'userRegion' => $record->country->isoCode,
                'region'     => $record->country->isoCode,
                'regionName'     => $record->country->name,
                'ipAddress'  => $ipAddress
            ];
            $this->cache->set($key, $result, Constant::CACHE_TTL_GEO_IP);
        } else {
            $result = $this->cache->get($key);
        }

        $this->logger->info('Ehlo succeed', ['result' => $result, 'ipAddress' => $ipAddress]);

//        print($record->country->isoCode . "\n"); // 'US'
//        print($record->country->name . "\n"); // 'United States'
//        print($record->country->names['es'] . "\n");
        $this->stopwatch->stop('GeoIpService::ehlo');
        return $result;
    }
}
