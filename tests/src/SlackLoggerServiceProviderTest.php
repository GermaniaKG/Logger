<?php
namespace tests;

use Germania\Logger\SlackLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;

class SlackLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$sut = new SlackLoggerServiceProvider("token", "channel", "username", 0);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function testMonologHandlers( )
	{
		$container = new Container;

		$sut = new SlackLoggerServiceProvider("token", "channel", "username", 0);
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}

}