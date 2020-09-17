<?php
namespace tests;

use Germania\Logger\StreamLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;
use Prophecy\PhpUnit\ProphecyTrait;

class StreamLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;
	public function testInstantiation()
	{
		$loglevel = 0;
		$sut = new StreamLoggerServiceProvider("", $loglevel);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function createSut()
	{
		$loglevel = 0;
		return new StreamLoggerServiceProvider("", $loglevel);
	}




	/**
	 * @dataProvider provideServicesAndInternalTypes
	 */
	public function testServiceFileTypes( $service, $expected_type)
	{
		$sut = $this->createSut();

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
        switch($expected_type):
            case "bool":
                $this->assertIsBool( $result );
                break;
            case "array":
                $this->assertIsArray( $result );
                break;
            case "callable":
                $this->assertIsCallable( $result );
                break;

            default:
                if (class_exists($expected_type)
                or interface_exists($expected_type)):
                    $this->assertInstanceOf( $expected_type, $result);
                    break;
                endif;

                $msg = sprintf("Expected type '%s' not supported in this test method", $expected_type);
                throw new \UnexpectedValueException( $msg );
        endswitch;
	}

	public function provideServicesAndInternalTypes()
	{
		return array(
			[ 'Monolog.Handlers', 'array' ]
		);
	}




	/**
	 * @dataProvider provideServicesAndInterfaces
	 */
	public function testServiceInterfaces( $service, $expected_interface)
	{
		$sut = $this->createSut();

		$container = new Container;
		$container->register( $sut );

		$result = $container[ $service ];
		$this->assertInstanceOf( $expected_interface, $result);
	}

	public function provideServicesAndInterfaces()
	{
		return array(
			[ 'Monolog.Handlers.StreamHandler', StreamHandler::class ]
		);
	}
}
