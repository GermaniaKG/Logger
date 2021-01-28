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


    /**
     * @dataProvider provideVariousLogLevels
     */
    public function testVariousLogLevels( $loglevel ) : BrowserConsoleLoggerServiceProvider
    {
        $sut = new BrowserConsoleLoggerServiceProvider( $loglevel );
        $this->assertInstanceOf( ServiceProviderInterface::class, $sut);
        return $sut;
    }

    public function provideVariousLogLevels() : array
    {
        return array(
            [ 100 ],
            [ LogLevel::INFO ],
            [ Logger::WARNING ]
        );
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
			[ 'Monolog.Handlers.BrowserConsoleHandler', BrowserConsoleHandler::class ]
		);
	}
}
