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

namespace Iods\Bunyan\Model\Config\Source;

class TablesToTruncate
{
    const CUSTOMER_VISITOR = 'customer_visitor';
    const REPORT_COMPARED = 'report_compared_product_index';
    const REPORT_EVENT = 'report_event';
    const REPORT_VIEWED = 'report_viewed_product_index';

    public function toArray(): array
    {
        $tables = [];

        return array_merge(
            $tables,
            self::CUSTOMER_VISITOR,
            self::REPORT_COMPARED,
            self::REPORT_EVENT,
            self::REPORT_VIEWED
        );
    }
}