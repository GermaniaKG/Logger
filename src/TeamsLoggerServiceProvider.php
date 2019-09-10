<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Formatter\HtmlFormatter;

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
     * @param string   $incoming_webook_url [description]
     * @param int|null $loglevel            [description]
     */
    public function __construct(string $incoming_webook_url, int $loglevel = null)
    {
        $this->incoming_webook_url = $incoming_webook_url;

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
            $handlers[] = $dic['Monolog.Handlers.TeamsHandler'];
            return $handlers;
        });



        /**
         * Send log messages to Microsoft Teams.
         *
         * @return SlackHandler
         */
        $dic['Monolog.Handlers.TeamsHandler'] = function ($dic) {
            $th = new HtmlFormattedTeamsLogHandler($this->incoming_webook_url, $this->loglevel);
            $th->setFormatter(new HtmlFormatter);
            return $th;
        };
    }
}
