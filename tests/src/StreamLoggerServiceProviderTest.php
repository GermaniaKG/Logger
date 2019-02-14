<?php
namespace tests;

use Germania\Logger\StreamLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;

class StreamLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel = 0;
		$sut = new StreamLoggerServiceProvider("", $loglevel);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function testMonologHandlers( )
	{

		$loglevel = 0;
		$sut = new StreamLoggerServiceProvider("", $loglevel);

		$container = new Container;
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}

}