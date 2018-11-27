<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

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
        $dic->extend('Monolog.Handlers', function(array $handlers, $dic) {
            $handlers[] = $dic['Monolog.Handlers.StreamHandler'];
            return $handlers;
        });


        /**
         * @return StreamHandler
         */
        $dic['Monolog.Handlers.StreamHandler'] = function( $dic) {
            $stream     = $this->stream;
            $loglevel   = $this->loglevel;

            $stream_handler = new StreamHandler($stream, $loglevel);

            $formatter = $dic['Monolog.Formatters.ColoredLineFormatter'];
            $stream_handler->setFormatter( $formatter );

            return $stream_handler;
        };


        /**
         * Taken from Monolog
         */
        $dic['Monolog.Handlers.StreamHandler.FormatLine'] = function( $dic ) {
            $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            return $format;
        };


        /**
         * @see  https://github.com/bramus/monolog-colored-line-formatter/blob/master/src/Formatter/ColoredLineFormatter.php
         * @return  ColoredLineFormatter from Bramus
         */
        $dic['Monolog.Formatters.ColoredLineFormatter'] = function( $dic )
        {
            $format = $dic['Monolog.Handlers.StreamHandler.FormatLine'];
            return new ColoredLineFormatter( null, $format, null, false, "ignore_empty" );
        };

    }
}
