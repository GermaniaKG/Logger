<?php
namespace tests;

use Germania\Logger\BrowserConsoleLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Prophecy\PhpUnit\ProphecyTrait;


class BrowserConsoleLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;



    public function testInstantiation() : BrowserConsoleLoggerServiceProvider
	{
		$sut = new BrowserConsoleLoggerServiceProvider( 100 );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
        return $sut;
	}


    public function testVariousLogLevels() : BrowserConsoleLoggerServiceProvider
    {
        $sut = new BrowserConsoleLoggerServiceProvider( $loglevel = LogLevel::INFO );
        $this->assertInstanceOf( ServiceProviderInterface::class, $sut);
        return $sut;
    }



    /**
     * @depends testInstantiation
     * @dataProvider provideServicesAndInterfaces
     */
    public function testWithArrays( $service, $expected_interface, $sut) : void
    {
        $array_dic = array();
        $array_dic = $sut->register($array_dic);

        $this->assertArrayHasKey( $service, $array_dic);
        $this->assertIsCallable($array_dic[$service]);

        $sut_pimple = new Container($array_dic);
        $this->assertInstanceOf( $expected_interface, $sut_pimple[$service]);
    }


	/**
     * @depends testInstantiation
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface, $sut) : void
	{

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces() : array
	{
		return array(
			[ BrowserConsoleHandler::class, BrowserConsoleHandler::class ]
		);
	}
}
