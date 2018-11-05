<?php
namespace Germania\Logger;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Monolog\Logger;
use Monolog\Processor\WebProcessor;

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
    public function __construct( string $logger_name, array $server_data = null, $anonymize_ip = true )
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
        $dic['Logger'] = function($dic) {
            $handlers   = $dic['Logger.Handlers'];
            $processors = $dic['Logger.Processors'];
            $title      = $this->logger_name;

            return new Logger( $title, $handlers, $processors);
        };




        /**
         * @return array
         */
        $dic['Logger.Environment'] = function($dic) {
            return $this->server_data;
        };


        /**
         * @return array
         */
        $dic['Logger.Handlers'] = function( $dic ) {
            return array();
        };



        /**
         * @return array
         */
        $dic['Logger.Processors'] = function($dic) {
            return array(
                $dic['Logger.Processors.WebProcessor']
            );
        };


        /**
         * @return WebProcessor
         */
        $dic['Logger.Processors.WebProcessor'] = function($dic) {
            $server_data  = $dic['Logger.Environment'];
            $extra_fields = $dic['Logger.Processors.Web.extraFields'];
            return new WebProcessor( $server_data, $extra_fields );
        };



        /**
         * Choose from url, ip, http_method, server, referrer
         *
         * @return array
         */
        $dic['Logger.Processors.Web.extraFields'] = function($dic) {
            return [
                'http_method',
                'url',
                'ip'
            ];
        };








    }

}
