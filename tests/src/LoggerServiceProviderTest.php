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


	public function testMonologHandlers( )
	{

		$sut = new LoggerServiceProvider("logname", array(), true);

		$container = new Container;
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}


	public function testMonologProcessors( )
	{

		$sut = new LoggerServiceProvider("logname", array(), true);

		$container = new Container;
		$container->register( $sut );

		$result = $container['Monolog.Processors'];
		$this->assertInternalType("array", $result);
	}


	public function testLoggerInterface( )
	{

		$sut = new LoggerServiceProvider("logname", array(), true);

		$container = new Container;
		$container->register( $sut );

		$result = $container['Logger'];
		$this->assertInstanceOf(LoggerInterface::class, $result);

		$result = $container['Monolog.Psr3Logger'];
		$this->assertInstanceOf(LoggerInterface::class, $result);
	}

}