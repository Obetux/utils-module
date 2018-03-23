<?php
/**
 * Created by PhpStorm.
 * User: cleyer
 * Date: 12/01/2018
 * Time: 11:06
 */

namespace App\Service;


use Monolog\Logger;

/**
 * Class WhitelistService
 * @package App\Service
 */
class WhitelistService
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * WhiteService constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    private function getList()
    {
        $array = [];
        $array['127.0.111.111'] = [
            'userRegion' => 'XX',
            'region'     => 'XX',
            'regionName'     => 'Argentina',
            'ipAddress'  => '192.0.111.111'
        ];
        return $array;
    }

    /**
     * @param $ipAddress
     * @return array|null
     */
    public function check($ipAddress): ?array
    {
        $array = $this->getList();
        if (array_key_exists($ipAddress, $array)) {
            return $array[$ipAddress];
        }
        return null;
    }
}