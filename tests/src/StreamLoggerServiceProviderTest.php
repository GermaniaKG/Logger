<?php
namespace tests;

use Germania\Logger\StreamLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Prophecy\PhpUnit\ProphecyTrait;

class StreamLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;


    /**
     * @dataProvider provideVariousLogLevels
     */
	public function testInstantiation( $loglevel ) : void
	{
		$sut = new StreamLoggerServiceProvider("", $loglevel);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}

    public function provideVariousLogLevels() : array
    {
        return array(
            [ 100 ],
            [ LogLevel::INFO ],
            [ Logger::WARNING ]
        );
    }

	public function createSut() : StreamLoggerServiceProvider
	{
		$loglevel = 0;
		return new StreamLoggerServiceProvider("", $loglevel);
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
			[ 'Monolog.Handlers.StreamHandler', StreamHandler::class ]
		);
	}
}
