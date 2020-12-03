<?php
namespace tests;

use Germania\Logger\BrowserConsoleLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\BrowserConsoleHandler;
use Prophecy\PhpUnit\ProphecyTrait;

class BrowserConsoleLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;

	public function testInstantiation()
	{
		$sut = new BrowserConsoleLoggerServiceProvider( 100 );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
        return $sut;
	}


	/**
     * @depends testInstantiation
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface, $sut)
	{

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces()
	{
		return array(
			[ 'Monolog.Handlers.BrowserConsoleHandler', BrowserConsoleHandler::class ]
		);
	}
}
