<?php
namespace tests;

use Germania\Logger\ClimateLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;

class ClimateLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel  = 100;
		$sut = new ClimateLoggerServiceProvider($loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function testMonologHandlers( )
	{

		$loglevel  = 100;
		$sut = new ClimateLoggerServiceProvider($loglevel );

		$container = new Container;
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}

}