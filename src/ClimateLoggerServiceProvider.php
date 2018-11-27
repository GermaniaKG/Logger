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
    public $loglevel = Logger::DEBUG;


    /**
     * @var string
     */
    public $loglevel_name = "debug";


    /**
     * @param int
     */
    public function __construct( int $loglevel )
    {
        $this->loglevel = $loglevel;

        $monolog_loglevel_name = MonologLogger::getLevelName( $loglevel );
        $this->loglevel_name = strtolower( $monolog_loglevel_name );
    }


    /**
     * @param  Container $dic [description]
     * @return void
     */
    public function register(Container $dic)
    {


        /**
         * @return array
         */
        $dic->extend('Monolog.Handlers', function(array $handlers, $dic) {
            $handlers[] = $dic['Climate.PsrLogger.MonologHandler'];
            return $handlers;
        });


        $dic['Climate.PsrLogger.MonologHandler'] = function($dic) {
            $climate_logger = $dic['Climate.PsrLogger'];
            return new PsrHandler( $climate_logger );
        };


        $dic['Climate.PsrLogger'] = function($dic) {
            $loglevel = $this->loglevel_name;
            return new CLImateLogger( $loglevel );
        };



    }
}
