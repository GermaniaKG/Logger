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
     * @param  \ArrayAccess|array $dic  DI Container
     * @return \ArrayAccess|array DI Container
     */
    public function register($dic)
    {

        // Do nothing when no incoming_webook_url is set
        if (empty($this->incoming_webook_url)) {
            return;
        }


        LoggerServiceProvider::addMonologHandler(TeamsLogHandler::class);

        /**
         * Send log messages to Microsoft Teams.
         *
         * @return TeamsLogHandler
         */
        $dic[TeamsLogHandler::class] = function ($dic) {
            $th = new TeamsLogHandler($this->incoming_webook_url, $this->loglevel);
            $th->setFormatter(new HtmlFormatter);
            return $th;
        };


        $dic['Monolog.Handlers.TeamsHandler'] = function ($dic) {
            return $dic[TeamsLogHandler::class];
        };

        return $dic;
    }
}
