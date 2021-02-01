<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;

class BrowserConsoleLoggerServiceProvider implements ServiceProviderInterface
{


    /**
     * @var int
     */
    public $loglevel = Logger::INFO;


    /**
     * @param int|null|string $loglevel Optional: Monolog or PSR-3 Loglevel constant. Set to `null` to disable browser logging entirely.
     */
    public function __construct($loglevel = null)
    {
        $this->loglevel = $loglevel;
    }


    /**
     * @param  \ArrayAccess|array $dic  DI Container
     * @return \ArrayAccess|array DI Container
     */
    public function register($dic)
    {
        // Do nothing when no loglevel is set
        if (empty($this->loglevel)) {
            return;
        }


        LoggerServiceProvider::addMonologHandler('Monolog.Handlers.BrowserConsoleHandler');


        /**
         * @return BrowserConsoleHandler
         */
        $dic['Monolog.Handlers.BrowserConsoleHandler'] = function ($dic) {
            $th = new BrowserConsoleHandler($this->loglevel);
            return $th;
        };
    }
}
