<?php
namespace tests;

use Germania\Logger\TeamsLoggerServiceProvider;
use CMDISP\MonologMicrosoftTeams\TeamsLogHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Prophecy\PhpUnit\ProphecyTrait;

class TeamsLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;



	public function testInstantiation( ) : TeamsLoggerServiceProvider
	{
		$sut = new TeamsLoggerServiceProvider("webhook", LogLevel::INFO );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        return $sut;
	}


	public function createSut() : TeamsLoggerServiceProvider
	{
		return new TeamsLoggerServiceProvider("logname", 0);
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
            [ TeamsLogHandler::class, TeamsLogHandler::class ]
		);
	}
}
