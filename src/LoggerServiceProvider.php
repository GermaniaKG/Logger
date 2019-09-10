<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
     * @param string  $logger_name  [description]
     * @param array   $server_data  [description]
     * @param boolean $anonymize_ip [description]
     */
    public function __construct(string $logger_name, array $server_data = null, $anonymize_ip = true)
    {
        $this->logger_name  = $logger_name;
        $this->server_data  = $server_data ?: $_SERVER;

        if ($anonymize_ip and isset($this->server_data['REMOTE_ADDR'])):
            $this->server_data['REMOTE_ADDR'] = preg_replace('/[0-9]+\z/', 'XXX', $this->server_data['REMOTE_ADDR']);
        endif;
    }


    /**
     * @return void
     */
    public function register(Container $dic)
    {

        /**
         * @return \Monolog\Logger
         */
        $dic['Logger'] = function ($dic) {
            return $dic['Monolog.Psr3Logger'];
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
         * @return MonologLogger
         */
        $dic['Monolog.Psr3Logger'] = function ($dic) {
            $handlers   = $dic['Monolog.Handlers'];
            $processors = $dic['Monolog.Processors'];
            $title      = $dic['Logger.name'];

            return new MonologLogger($title, $handlers, $processors);
        };


        /**
         * @return array
         */
        $dic['Monolog.Handlers'] = function ($dic) {
            return array();
        };



        /**
         * @return array
         */
        $dic['Monolog.Processors'] = function ($dic) {
            return array(
                $dic['Monolog.Processors.PsrLogMessages'],
                $dic['Monolog.Processors.WebProcessor']
            );
        };


        /**
         * @see https://github.com/Seldaek/monolog/blob/master/src/Monolog/Processor/PsrLogMessageProcessor.php
         * @return PsrLogMessageProcessor
         */
        $dic['Monolog.Processors.PsrLogMessages'] = function ($dic) {
            return new PsrLogMessageProcessor(null, true);
        };


        /**
         * @return WebProcessor
         */
        $dic['Monolog.Processors.WebProcessor'] = function ($dic) {
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
    }
}
