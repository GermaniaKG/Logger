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
     * @param int|null $loglevel Monolog Loglevel nulber or NULL to disable
     */
    public function __construct(int $loglevel = null)
    {
        $this->loglevel = $loglevel;
    }


    /**
     * @param  Container $dic [description]
     * @return void
     */
    public function register(Container $dic)
    {
        // Do nothing when no loglevel is set
        if (empty($this->loglevel)) {
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
            $handlers[] = $dic['Monolog.Handlers.BrowserConsoleHandler'];
            return $handlers;
        });



        /**
         * @return BrowserConsoleHandler
         */
        $dic['Monolog.Handlers.BrowserConsoleHandler'] = function ($dic) {
            $th = new BrowserConsoleHandler($this->loglevel);
            return $th;
        };
    }
}
