<?php
namespace tests;

use Germania\Logger\FileLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\RotatingFileHandler;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LogLevel;
use Monolog\Logger;

class FileLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;


    /**
     * @dataProvider provideVariousLogLevels
     */
	public function testInstantiation( $loglevel ) : void
	{
		$max_files = 0;
		$sut = new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}

    public function provideVariousLogLevels() : array
    {
        return array(
            [ 0 ],
            [ LogLevel::INFO ],
            [ Logger::WARNING ]
        );
    }


	public function createSut() : FileLoggerServiceProvider
	{
		$loglevel  = 0;
		$max_files = 0;
		return new FileLoggerServiceProvider("file", $max_files = 0, $loglevel );

	}






	/**
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface) : void
	{
		$sut = $this->createSut();

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces() : array
	{
		return array(
			[ 'Monolog.Handlers.RotatingFileHandler', RotatingFileHandler::class ]
		);
	}
}
