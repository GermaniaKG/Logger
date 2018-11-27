<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\SwiftMailerHandler;


class SwiftMailerLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var int
     */
    public $loglevel = Logger::WARNING;


    /**
     * @param int|null $loglevel [description]
     */
    public function __construct( int $loglevel = null )
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

        if (!$dic->offsetExists( "SwiftMailer")) {
            throw new \RuntimeException("This service provider requires a 'SwiftMailer' service.");
        }

        if (!$dic->offsetExists( "SwiftMailer.HtmlMessage")) {
            throw new \RuntimeException("This service provider requires a 'SwiftMailer.HtmlMessage' service.");
        }


        /**
         * @return array
         */
        $dic->extend('Monolog.Handlers', function(array $handlers, $dic) {
            $handlers[] = $dic['Monolog.Handlers.SwiftMailerHandler'];
            return $handlers;
        });


        /**
         * Send log messages per mail, combining three Monolog handlers:
         *
         * 1. FingersCrossedHandler:
         *    When a certain level has been reached ...
         *
         * 2. BufferHandler:
         *    Buffers all records until closing the handler [i.e. script end],
         *    then pass them as batch [to the wrapped handler].
         *
         * 3. SwiftMailerHandler:
         *    Send all [buffered] messages with one mail
         *
         * @return FingersCrossedHandler
         */
        $dic['Monolog.Handlers.SwiftMailerHandler'] = function( $dic) {
            $mailer   = $dic['SwiftMailer'];

            // The mail subject will be used for LineFormatter
            // https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php
            $message  = $dic['SwiftMailer.HtmlMessage'];
            $message->setSubject("[%channel%.%level_name%] %message%\n");

            // Set loglevel for this handler to DEBUG to gather ALL information
            $mailerHandler = new SwiftMailerHandler( $mailer, $message, Logger::DEBUG );
            $mailerHandler->setFormatter( new HtmlFormatter );

            // Build handler "sandwich"
            return new FingersCrossedHandler( new BufferHandler( $mailerHandler ), $this->loglevel);
        };


    }
}
