<?php
namespace tests;

use Germania\Logger\TeamsLoggerServiceProvider;
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


	public function testMonologHandlers( )
	{
		$container = new Container;

		$sut = new TeamsLoggerServiceProvider("webhook", 0);
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}

}