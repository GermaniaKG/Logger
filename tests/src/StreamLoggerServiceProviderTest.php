<?php
namespace tests;

use Germania\Logger\StreamLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;

class StreamLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel = 0;
		$sut = new StreamLoggerServiceProvider("", $loglevel);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function createSut()
	{
		$loglevel = 0;
		return new StreamLoggerServiceProvider("", $loglevel);
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
			[ 'Monolog.Handlers.StreamHandler', StreamHandler::class ]
		);
	}	
}