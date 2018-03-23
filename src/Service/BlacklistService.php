<?php
/**
 * Created by PhpStorm.
 * User: cleyer
 * Date: 12/01/2018
 * Time: 11:05
 */

namespace App\Service;
use App\Exception\BlacklistException;
use Monolog\Logger;

/**
 * Class BlacklistService
 * @package App\Service
 */
class BlacklistService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * BlacklistService constructor.
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
        $array[] = '192.0.111.111';
        return $array;
    }

    /**
     * @param $ipAddress
     * @return bool
     * @throws BlacklistException
     */
    public function check($ipAddress)
    {
        $array = $this->getList();
        if (in_array($ipAddress, $array)) {
            $this->logger->warning('IP in Blacklist', ['ipAddress' => $ipAddress]);
            throw new BlacklistException();
        }
        return false;
    }
}