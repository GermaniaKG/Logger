<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Handler\SlackHandler;

class SlackLoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    public $token;

    /**
     * @var string
     */
    public $channel;

    /**
     * @var string
     */
    public $username;

    /**
     * @var int
     */
    public $loglevel = Logger::CRITICAL;


    /**
     * @param string          $token     Slack token
     * @param string          $channel   Slack channel
     * @param string          $username  Slack username
     * @param int|null|string $loglevel  Optional: Monolog or PSR-3 Loglevel constant
     */
    public function __construct(string $token, string $channel, string $username, $loglevel = null)
    {
        $this->token    = $token;
        $this->channel  = $channel;
        $this->username = $username;

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


        // Do nothing when no Slack channel is set
        if (empty($this->channel)) {
            return;
        }


        LoggerServiceProvider::addMonologHandler(SlackHandler::class);



        /**
         * @deprecated
         */
        $dic['Monolog.Handlers.SlackHandler'] = function ($dic) {
            return $dic[SlackHandler::class];
        };



        /**
         * Send log messages to Slack.
         *
         * - https://blog.tschelhas.de/symfony/slack-als-logger-fuer-symfony-nutzen/
         * - https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SlackHandler.php
         *
         * @return SlackHandler
         */
        $dic[SlackHandler::class] = function ($dic) {

            // As hardcoded in SlackHandler class
            $useAttachment          = true;              // Whether the message should be added to Slack as attachment (plain text otherwise)
            $iconEmoji              = null;              // The emoji name to use (or null)
            $loglevel               = Logger::CRITICAL;  // The minimum logging level at which this handler will be triggered
            $bubble                 = true;              // Whether the messages that are handled can bubble up the stack or not
            $useShortAttachment     = false;             // Whether the the context/extra messages added to Slack as attachments are in a short style
            $includeContextAndExtra = false;             // Whether the attachment should include context and extra data
            $excludeFields          = array();           // Dot separated list of fields to exclude from slack message. E.g. ['context.field1', 'extra.field2']

            // Override with custom values
            $loglevel               = $this->loglevel;   // Override for custom loglevel
            $includeContextAndExtra = true;              // Show details in Slack
            $useShortAttachment     = true;              // Show details in Slack as compact JSON code boxes

            // Slack credentials
            $token    = $this->token;
            $channel  = $this->channel;
            $username = $this->username;

            return new SlackHandler($token, $channel, $username, $useAttachment, $iconEmoji, $loglevel, $bubble, $useShortAttachment, $includeContextAndExtra, $excludeFields);
        };


        return $dic;
    }
}
