<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\PsrHandler;
use League\CLImate\Logger as CLImateLogger;

class ClimateLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var int
     */
    public $loglevel = MonologLogger::DEBUG;


    /**
     * @var string
     */
    public $loglevel_name;


    /**
     * @param int|string Monolog or PSR-3 Loglevel constant.
     */
    public function __construct($loglevel)
    {
        $this->loglevel = $loglevel;

        if (is_int($loglevel)) {
            $monolog_loglevel_name = MonologLogger::getLevelName($loglevel);
            $this->loglevel_name = strtolower($monolog_loglevel_name);
        }
        elseif (is_string($loglevel)) {
            $this->loglevel_name = strtolower($loglevel);
        }
        else {
            throw new \InvalidArgumentException("Monolog or PSR-3 Loglevel constant.");
        }
    }


    /**
     * @param  \ArrayAccess|array $dic  DI Container
     * @return \ArrayAccess|array DI Container
     */
    public function register($dic)
    {

        LoggerServiceProvider::addMonologHandler('Climate.PsrLogger.MonologHandler');


        $dic['Climate.PsrLogger.MonologHandler'] = function ($dic) {
            $climate_logger = $dic['Climate.PsrLogger'];
            return new PsrHandler($climate_logger);
        };


        $dic['Climate.PsrLogger'] = function ($dic) {
            $loglevel = $this->loglevel_name;
            return new CLImateLogger($loglevel);
        };
    }
}
