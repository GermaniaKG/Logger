<?php
namespace tests;

use Germania\Logger\ClimateLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\PsrHandler;
use League\CLImate\Logger as CLImateLogger;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LogLevel;
use Monolog\Logger;

class ClimateLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;


    /**
     * @dataProvider provideVariousLogLevels
     */
    public function testInstantiation($loglevel ) : void
	{
		$sut = new ClimateLoggerServiceProvider($loglevel );
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


	public function createSut() : ClimateLoggerServiceProvider
	{
		$loglevel  = 100;
		return new ClimateLoggerServiceProvider($loglevel );
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
			[ 'Climate.PsrLogger.MonologHandler', PsrHandler::class ],
			[ 'Climate.PsrLogger', CLImateLogger::class ]
		);
	}
}
