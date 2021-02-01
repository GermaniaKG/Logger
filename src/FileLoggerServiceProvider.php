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
     * @param string|null      $logfile    Logfile path
     * @param int|null         $max_files  Maximum number of logfiles
     * @param int|null|string  $loglevel   Optional: Monolog  or PSR-3 Loglevel constant
     */
    public function __construct(string $logfile = null, int $max_files = null, $loglevel = null)
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
     * @param  \ArrayAccess|array $dic  DI Container
     * @return \ArrayAccess|array DI Container
     */
    public function register($dic)
    {
        // Do nothing when no logfile is set
        if (empty($this->logfile)) {
            return $dic;
        }


        LoggerServiceProvider::addMonologHandler(RotatingFileHandler::class);


        $dic['Monolog.Handlers.RotatingFileHandler'] = function ($dic) {
            return $dic[RotatingFileHandler::class];
        };


        /**
         * @return RotatingFileHandler
         */
        $dic[RotatingFileHandler::class] = function ($dic) {
            $logfile   = $this->logfile;
            $max_files = $this->max_files;
            $loglevel  = $this->loglevel;

            return new RotatingFileHandler($logfile, $max_files, $loglevel);
        };


        return $dic;
    }
}
