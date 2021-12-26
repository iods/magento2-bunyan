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

namespace Iods\Bunyan\Model;

use Exception;
use Iods\Bunyan\Api\TruncateTablesInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

class TruncateTables implements TruncateTablesInterface
{
    protected AdapterInterface $_connection;

    protected Config $_config;

    protected LoggerInterface $_logger;

    public function __construct(
        AdapterInterface $connection,
        Config $config,
        LoggerInterface $logger,
    ) {
        $this->_connection = $connection;
        $this->_config = $config;
        $this->_logger = $logger;
    }

    public function execute(): void
    {
        $tables = $this->_config->getTablesToTruncate();
        foreach ($tables as $t) {
            // truncate the tables
            try {
                $this->_logger->info(__('Attempt to truncate %1', $t));
                $this->_connection->truncateTable($t);
                $this->_logger->info(__('%1 table has been truncated.', $t));
            } catch (Exception $e) {
                $this->_logger->info(__('Failed to truncate %1 : %2 ', $t, $e->getMessage()));
                $this->_logger->critical($e);
            }
        }
    }
}