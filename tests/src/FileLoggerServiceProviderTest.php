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


	public function testInstantiation( ) : FileLoggerServiceProvider
	{
		$sut = new FileLoggerServiceProvider("file", $max_files = 0, $logelevel = LogLevel::INFO );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        return $sut;
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
			[ RotatingFileHandler::class, RotatingFileHandler::class ]
		);
	}
}
