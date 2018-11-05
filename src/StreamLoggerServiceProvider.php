<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class StreamLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    public $stream = "php://stderr";


    /**
     * @var int
     */
    public $loglevel = Logger::WARNING;


    /**
     * @param string|null $stream   [description]
     * @param int|null    $loglevel [description]
     */
    public function __construct( string $stream = null, int $loglevel = null )
    {
        if (!is_null($stream)) {
            $this->stream = $stream;
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
            $handlers[] = $dic['Logger.Handlers.StreamHandler'];
            return $handlers;
        });


        /**
         * @return StreamHandler
         */
        $dic['Logger.Handlers.StreamHandler'] = function( $dic) {
            $stream     = $this->stream;
            $loglevel   = $this->loglevel;

            return new StreamHandler($stream, $loglevel);
        };

    }
}
