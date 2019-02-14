<?php
namespace tests;

use Germania\Logger\FileLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;

class FileLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel  = 0;
		$max_files = 0;
		$sut = new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function testMonologHandlers( )
	{

		$loglevel  = 0;
		$max_files = 0;
		$sut = new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );

		$container = new Container;
		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}

}