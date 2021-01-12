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
     * @param string|null $logfile   Logfile path
     * @param int|null    $max_files Maximum number of logfiles
     * @param int|null    $loglevel  Monolog Loglevel constant
     */
    public function __construct(string $logfile = null, int $max_files = null, int $loglevel = null)
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
        // Do nothing when no logfile is set
        if (empty($this->logfile)) {
            return;
        }


        // Make sure there's a 'Monolog.Handlers' service
        if (!$dic->offsetExists('Monolog.Handlers')) :
            $dic['Monolog.Handlers'] = function ($dic) {
                return array();
            };
        endif;


        /**
         * @return array
         */
        $dic->extend('Monolog.Handlers', function (array $handlers, $dic) {
            $handlers[] = $dic['Monolog.Handlers.RotatingFileHandler'];
            return $handlers;
        });


        /**
         * @return RotatingFileHandler
         */
        $dic['Monolog.Handlers.RotatingFileHandler'] = function ($dic) {
            $logfile   = $this->logfile;
            $max_files = $this->max_files;
            $loglevel  = $this->loglevel;

            return new RotatingFileHandler($logfile, $max_files, $loglevel);
        };
    }
}
