<?php
/**
 * Bigger, better logging manager for Magento 2
 *
 * @category  Iods
 * @version   000.1.0
 * @author    Rye Miller <rye@drkstr.dev>
 * @copyright Copyright (c) 2021, Rye Miller (http://ryemiller.io)
 * @license   MIT (https://en.wikipedia.org/wiki/MIT_License)
 */
declare(strict_types=1);

namespace Iods\Bunyan\Cron;

use Iods\Bunyan\Model\Config;
use Iods\Bunyan\Model\TruncateTables;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class Purge
{
    protected Config $_config;

    protected DateTime $_dateTime;

    protected LoggerInterface $_logger;

    protected TruncateTables $_truncateTables;

    public function __construct(
        Config          $config,
        DateTime        $dateTime,
        LoggerInterface $logger,
        TruncateTables  $truncateTables
    ) {
        $this->_config = $config;
        $this->_dateTime = $dateTime;
        $this->_logger = $logger;
        $this->_truncateTables = $truncateTables;
    }

    public function execute(): void
    {
        if ($this->_config->getCron()) {
            $this->_logger->info(__('%1 Purge Cronjob Start', $this->_dateTime->gmtDate()));
            $this->_truncateTables->execute();
            $this->_logger->info(__('%1 Purge Cronjob Finish', $this->_dateTime->gmtDate()));
        }
    }
}