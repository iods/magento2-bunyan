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

use Iods\Bunyan\Api\ConfigInterface;
use Iods\Bunyan\Model\Config\Source\TablesToTruncate;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ConfigInterface
{
    const CONFIG_XML_PATH = 'bunyan/log_purge/cron';

    protected ScopeConfigInterface $_scopeConfig;

    protected TablesToTruncate $_tablesToTruncate;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TablesToTruncate $tablesToTruncate
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_tablesToTruncate = $tablesToTruncate;
    }

    public function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag('enabled');
    }

    public function getCron(): string
    {
        return $this->_scopeConfig->getValue(self::CONFIG_XML_PATH, ScopeInterface::SCOPE_STORE);
    }

    public function getTablesToTruncate(): array
    {
        return $this->_tablesToTruncate->toArray();
    }
}