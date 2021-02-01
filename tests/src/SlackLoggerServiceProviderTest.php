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



    public function testInstantiation( ) : ServiceProviderInterface
	{
		$sut = new SlackLoggerServiceProvider("token", "channel", "username", $loglevel = LogLevel::INFO);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        return $sut;
	}



	public function createSut() : SlackLoggerServiceProvider
	{
		return new SlackLoggerServiceProvider("token", "channel", "username", 0);
	}



    /**
     * @depends testInstantiation
     * @dataProvider provideServicesAndInterfaces
     */
    public function testWithArrays( $service, $expected_interface, $sut) : void
    {
        $array_dic = array();
        $array_dic = $sut->register($array_dic);

        $this->assertArrayHasKey( $service, $array_dic);
        $this->assertIsCallable($array_dic[$service]);

        $sut_pimple = new Container($array_dic);
        $this->assertInstanceOf( $expected_interface, $sut_pimple[$service]);
    }


	/**
     * @depends testInstantiation
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface, $sut) : void
	{
		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces() : array
	{
		return array(
			[ SlackHandler::class, SlackHandler::class ]
		);
	}
}
