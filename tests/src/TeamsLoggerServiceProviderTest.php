<?php
namespace tests;

use Germania\Logger\TeamsLoggerServiceProvider;
use Germania\Logger\HtmlFormattedTeamsLogHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;

class TeamsLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$sut = new TeamsLoggerServiceProvider("webhook", 0);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}



	public function createSut()
	{
		return new TeamsLoggerServiceProvider("logname", 0);
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
			[ 'Monolog.Handlers.TeamsHandler', HtmlFormattedTeamsLogHandler::class ]
		);
	}	
}