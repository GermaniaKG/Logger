<?php
namespace tests;

use Germania\Logger\FileLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\RotatingFileHandler;

class FileLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel  = 0;
		$max_files = 0;
		$sut = new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function createSut()
	{
		$loglevel  = 0;
		$max_files = 0;
		return new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );

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
			[ 'Monolog.Handlers.RotatingFileHandler', RotatingFileHandler::class ]
		);
	}	
}