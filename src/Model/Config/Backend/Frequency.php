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

namespace Iods\Bunyan\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Frequency extends Value
{
    const CRON_STRING_PATH = 'crontab/default/jobs/iods_bunyan_purge/schedule/timer';

    const CRON_MODEL_PATH = 'crontab/default/jobs/iods_bunyan/purge/run/model';

    protected ValueFactory $_valueFactory;

    protected string $_runModelPath = '';

    public function __construct(
        AbstractResource $abstractResource = null,
        AbstractDb $abstractDb = null,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $typeList,
        ValueFactory $valueFactory,
        $runModelPath = '',
        array $data = []

    ) {
        $this->_runModelPath = $runModelPath;
        $this->_valueFactory = $valueFactory;
        parent::__construct(
            $context,
            $registry,
            $scopeConfig,
            $typeList,
            $abstractResource,
            $abstractDb,
            $data
        );
    }

    public function afterSave(): Frequency
    {
        $t = $this->getData('groups/log_purge/fields/time/value');
        $f = $this->getData('groups/log_purge/fields/frequency/value');

        $cronArray = [
            (int) $t[1], // minute
            (int) $t[0], // hour
            $f == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*', // day of month
            '*', // month of year
            $f == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*' // day of week
        ];

        $cronString = join(' ', $cronArray);

        try {
            $this->_valueFactory
                ->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue($cronString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            $this->_valueFactory
                ->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue($this->_runModelPath)
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (\Exception $e) {
            throw new LocalizedException(__('We cannot save this cron expression.'));
        }

        return parent::afterSave();
    }
}