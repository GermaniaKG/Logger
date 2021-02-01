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


	public function testInstantiation( ) : StreamLoggerServiceProvider
	{
		$sut = new StreamLoggerServiceProvider("", $loglevel = LogLevel::INFO );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        return $sut;
	}



	public function createSut() : StreamLoggerServiceProvider
	{
		$loglevel = 0;
		return new StreamLoggerServiceProvider("", $loglevel);
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
			[ StreamHandler::class, StreamHandler::class ]
		);
	}
}
