<?php
namespace tests;

use Germania\Logger\ClimateLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\PsrHandler;
use League\CLImate\Logger as CLImateLogger;

class ClimateLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel  = 100;
		$sut = new ClimateLoggerServiceProvider($loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}



	public function createSut()
	{
		$loglevel  = 100;
		return new ClimateLoggerServiceProvider($loglevel );
	}



	/**
	 * @dataProvider provideServicesAndInternalTypes
	 */
	public function testServiceFileTypes( $service, $internal_type)
	{
		$sut = $this->createSut();

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInternalType( $internal_type, $result);
	}

	public function provideServicesAndInternalTypes()
	{
		return array(
			[ 'Monolog.Handlers', 'array' ]
		);
	}



	/**
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface)
	{
		$sut = $this->createSut();

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces()
	{
		return array(
			[ 'Climate.PsrLogger.MonologHandler', PsrHandler::class ],
			[ 'Climate.PsrLogger', CLImateLogger::class ]
		);
	}	
}