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

namespace Iods\Bunyan\Console\Command;

use Iods\Bunyan\Model\TruncateTables;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Purge extends Command
{
    const LIMIT_OPT = 'limit';
    const PURGE_ARG = 'purge';

    private DateTime $_dateTime;

    private InputInterface $_input;

    private LoggerInterface $_logger;

    private OutputInterface $_output;

    private State $_state;

    private TruncateTables $_truncateTables;

    public function __construct(
        DateTime $dateTime,
        LoggerInterface $logger,
        State $state,
        TruncateTables $truncateTables
    ) {
        $this->_dateTime = $dateTime;
        $this->_logger = $logger;
        $this->_state = $state;
        $this->_truncateTables = $truncateTables;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('iods:logs');
        $this->setDescription('Purge and clean log entries from the Magento database.');
        $this->setDefinition([
            new InputArgument(self::PURGE_ARG, InputArgument::REQUIRED, 'Purge'),
        ]);
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;
        $this->_state->setAreaCode(Area::AREA_GLOBAL);
        $purge = $input->getArgument(self::PURGE_ARG);

        if ($purge) {
            $this->_output->writeln((string) __('[%1] Start', $this->_dateTime->gmtDate()));
            $this->_truncateTables->execute();
            $this->_output->writeln((string) __('[%1] finish', $this->_dateTime->gmtDate()));
        }
    }
}