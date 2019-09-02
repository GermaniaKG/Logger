<?php
namespace tests;

use Germania\Logger\LoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Psr\Log\LoggerInterface;

class LoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	/**
	 * @dataProvider provideCtorArgs
	 */
	public function testInstantiation( $logname, $server, $anonymize )
	{
		
		$sut = new LoggerServiceProvider($logname, $server, $anonymize);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}

	public function provideCtorArgs()
	{
		return array(
			[ "logname", array(), true ],
			[ "logname", array(), false ],
			[ "logname", array( 'REMOTE_ADDR' => '127.0.0.1'), false ],
			[ "logname", array( 'REMOTE_ADDR' => '127.0.0.1'), true ]
			
		);
	}


	/**
	 * @dataProvider provideServicesAndInternalTypes
	 */
	public function testServiceFileTypes( $service, $internal_type)
	{

		$sut = new LoggerServiceProvider("logname", array(), true);

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInternalType( $internal_type, $result);
	}

	public function provideServicesAndInternalTypes()
	{
		return array(
			[ 'Monolog.Handlers', 'array' ],
			[ 'Monolog.Processors', 'array' ]
		);
	}



	/**
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface)
	{

		$sut = new LoggerServiceProvider("logname", array(), true);

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces()
	{
		return array(
			[ 'Logger',             LoggerInterface::class ],
			[ 'Monolog.Psr3Logger', LoggerInterface::class ]
		);
	}


}