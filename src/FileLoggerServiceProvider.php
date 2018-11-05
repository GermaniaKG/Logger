<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class FileLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    public $logfile = "php://stderr";

    /**
     * @var int
     */
    public $max_files = 30;

    /**
     * @var int
     */
    public $loglevel = Logger::DEBUG;


    /**
     * @param string|null $logfile   [description]
     * @param int|null    $max_files [description]
     * @param int|null    $loglevel  [description]
     */
    public function __construct( string $logfile = null, int $max_files = null, int $loglevel = null )
    {
        $this->logfile = $logfile;

        if (!is_null($max_files)) {
            $this->max_files = $max_files;
        }

        if (!is_null($loglevel)) {
            $this->loglevel = $loglevel;
        }
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
        $dic->extend('Logger.Handlers', function(array $handlers, $dic) {
            $handlers[] = $dic['Logger.Handlers.RotatingFileHandler'];
            return $handlers;
        });


        /**
         * @return RotatingFileHandler
         */
        $dic['Logger.Handlers.RotatingFileHandler'] = function( $dic) {

            $logfile   = $this->logfile;
            $max_files = $this->max_files;
            $loglevel  = $this->loglevel;

            return new RotatingFileHandler($logfile, $max_files, $loglevel);
        };


    }
}
