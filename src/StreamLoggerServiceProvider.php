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
     * @param string|null      $stream    PHP Stream name
     * @param int|null|string  $loglevel  Optional: Monolog or PSR-3 Loglevel constant
     */
    public function __construct(string $stream = null, $loglevel = null)
    {
        if (!is_null($stream)) {
            $this->stream = $stream;
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


        LoggerServiceProvider::addMonologHandler(StreamHandler::class);




        /**
         * @return StreamHandler
         */
        $dic['Monolog.Handlers.StreamHandler'] = function ($dic) {
            return $dic[StreamHandler::class];
        };


        /**
         * @return StreamHandler
         */
        $dic[StreamHandler::class] = function ($dic) {
            $stream     = $this->stream;
            $loglevel   = $this->loglevel;

            $stream_handler = new StreamHandler($stream, $loglevel);

            $formatter = $dic[ColoredLineFormatter::class];
            $stream_handler->setFormatter($formatter);

            return $stream_handler;
        };



        /**
         * Taken from Monolog
         */
        $dic['Monolog.Handlers.StreamHandler.FormatLine'] = function ($dic) {
            $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            return $format;
        };


        /**
         * @see  https://github.com/bramus/monolog-colored-line-formatter/blob/master/src/Formatter/ColoredLineFormatter.php
         * @return  ColoredLineFormatter from Bramus
         */
        $dic[ColoredLineFormatter::class] = function ($dic) {
            $format = $dic['Monolog.Handlers.StreamHandler.FormatLine'];
            return new ColoredLineFormatter(null, $format, null, false, "ignore_empty");
        };

        return $dic;
    }
}
