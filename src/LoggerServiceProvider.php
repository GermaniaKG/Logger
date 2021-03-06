<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Psr\Log\LoggerInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\PsrLogMessageProcessor;

class LoggerServiceProvider implements ServiceProviderInterface
{

    /**
     * @var string
     */
    public $logger_name  = "Logger";

    /**
     * @var array
     */
    public $server_data  = array();


    /**
     * @var array
     */
    public static $monolog_handler_definitions = array();



    /**
     * @param string   $logger_name    App or logger name
     * @param array    $server_data    Server data, default is `$_SERVER`
     * @param boolean  $anonymize_ip   Whether to anonymize client IPs
     */
    public function __construct(string $logger_name, array $server_data = null, $anonymize_ip = true)
    {
        $this->logger_name  = $logger_name;
        $this->server_data  = $server_data ?: $_SERVER;

        if ($anonymize_ip and isset($this->server_data['REMOTE_ADDR'])):
            $this->server_data['REMOTE_ADDR'] = preg_replace('/[0-9]+\z/', 'XXX', $this->server_data['REMOTE_ADDR']);
        endif;
    }


    public static function addMonologHandler( string $def )
    {
        static::$monolog_handler_definitions[] = $def;
    }


    public static function resetMonologHandlers()
    {
        static::$monolog_handler_definitions = array();
    }



    /**
     * @param  \ArrayAccess|array $dic  DI Container
     * @return \ArrayAccess|array DI Container
     */
    public function register($dic)
    {

        if (!is_array($dic) and !$dic instanceOf \ArrayAccess) {
            throw new \InvalidArgumentException("Array or ArrayAccess expected");
        }


        /**
         * @return \Monolog\Logger
         */
        $dic[LoggerInterface::class] = function ($dic) {
            return $dic[MonologLogger::class];
        };



        /**
         * @return \Monolog\Logger
         */
        $dic['Logger'] = function ($dic) {
            return $dic[MonologLogger::class];
        };



        /**
         * @return string
         */
        $dic['Logger.name'] = function ($dic) {
            return $this->logger_name;
        };


        /**
         * @return array
         */
        $dic['Logger.Environment'] = function ($dic) {
            return $this->server_data;
        };


        /**
         * @deprecated
         */
        $dic['Monolog.Psr3Logger'] = function ($dic) {
            return $dic[MonologLogger::class];
        };


        /**
         * @return MonologLogger
         */
        $dic[MonologLogger::class] = function ($dic) {
            $handlers   = $dic['Monolog.Handlers'];
            $processors = $dic['Monolog.Processors'];
            $title      = $dic['Logger.name'];

            return new MonologLogger($title, $handlers, $processors);
        };


        /**
         * @return array
         */
        $dic['Monolog.Handlers'] = function ($dic) {
            return array_map(function($def) use ($dic) {
                return $dic[$def];
            }, static::$monolog_handler_definitions);
        };



        /**
         * @return array
         */
        $dic['Monolog.Processors'] = function ($dic) {
            return array(
                $dic[PsrLogMessageProcessor::class],
                $dic[WebProcessor::class]
            );
        };


        /**
         * @see https://github.com/Seldaek/monolog/blob/master/src/Monolog/Processor/PsrLogMessageProcessor.php
         * @return PsrLogMessageProcessor
         */
        $dic[PsrLogMessageProcessor::class] = function ($dic) {
            return new PsrLogMessageProcessor(null, true);
        };



        /**
         * @return WebProcessor
         */
        $dic[WebProcessor::class] = function ($dic) {
            $server_data  = $dic['Logger.Environment'];
            $extra_fields = $dic['Monolog.Processors.WebProcessor.extraFields'];
            return new WebProcessor($server_data, $extra_fields);
        };




        /**
         * Choose from url, ip, http_method, server, referrer
         *
         * @return array
         */
        $dic['Monolog.Processors.WebProcessor.extraFields'] = function ($dic) {
            return [
                'http_method',
                'url',
                'ip'
            ];
        };

        return $dic;
    }
}
