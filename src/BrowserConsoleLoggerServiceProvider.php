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
     * @param string   $incoming_webook_url [description]
     * @param int|null $loglevel            [description]
     */
    public function __construct(int $loglevel = null)
    {
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
