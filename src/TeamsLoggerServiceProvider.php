<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Formatter\HtmlFormatter;
use CMDISP\MonologMicrosoftTeams\TeamsLogHandler;


class TeamsLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    public $incoming_webook_url;

    /**
     * @var int
     */
    public $loglevel = Logger::INFO;


    /**
     * @param string           $incoming_webook_url Incoming Webhook URL, leave empty to disable
     * @param int|null|string  $loglevel            Optional: Monolog or PSR-3 Loglevel constant
     */
    public function __construct(string $incoming_webook_url = null, $loglevel = null)
    {
        $this->incoming_webook_url = $incoming_webook_url;
        $this->loglevel = $loglevel ?: Logger::INFO;
    }


    /**
     * @param  Container $dic [description]
     * @return void
     */
    public function register(Container $dic)
    {

        // Do nothing when no incoming_webook_url is set
        if (empty($this->incoming_webook_url)) {
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
            $handlers[] = $dic['Monolog.Handlers.TeamsHandler'];
            return $handlers;
        });



        /**
         * Send log messages to Microsoft Teams.
         *
         * @return SlackHandler
         */
        $dic['Monolog.Handlers.TeamsHandler'] = function ($dic) {
            $th = new TeamsLogHandler($this->incoming_webook_url, $this->loglevel);
            $th->setFormatter(new HtmlFormatter);
            return $th;
        };
    }
}
