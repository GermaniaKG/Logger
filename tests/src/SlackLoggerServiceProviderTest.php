<?php
namespace tests;

use Germania\Logger\SlackLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Prophecy\PhpUnit\ProphecyTrait;

class SlackLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;



    /**
     * @dataProvider provideVariousLogLevels
     */
    public function testInstantiation( $loglevel ) : void
	{
		$sut = new SlackLoggerServiceProvider("token", "channel", "username", $loglevel);
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


	public function createSut() : SlackLoggerServiceProvider
	{
		return new SlackLoggerServiceProvider("token", "channel", "username", 0);
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
			[ 'Monolog.Handlers.SlackHandler', SlackHandler::class ]
		);
	}
}
