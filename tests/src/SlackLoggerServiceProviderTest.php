<?php
namespace tests;

use Germania\Logger\SlackLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\SlackHandler;
use Prophecy\PhpUnit\ProphecyTrait;

class SlackLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

	public function testInstantiation() : void
	{
		$sut = new SlackLoggerServiceProvider("token", "channel", "username", 0);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function createSut() : SlackLoggerServiceProvider
	{
		return new SlackLoggerServiceProvider("token", "channel", "username", 0);
	}



	/**
	 * @dataProvider provideServicesAndInternalTypes
	 */
	public function testServiceFileTypes( $service, $expected_type) : void
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

	public function provideServicesAndInternalTypes() : array
	{
		return array(
			[ 'Monolog.Handlers', 'array' ]
		);
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
