<?php
namespace tests;

use Germania\Logger\TeamsLoggerServiceProvider;
use Germania\Logger\HtmlFormattedTeamsLogHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Prophecy\PhpUnit\ProphecyTrait;

class TeamsLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;

	public function testInstantiation()
	{
		$sut = new TeamsLoggerServiceProvider("webhook", 0);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}



	public function createSut()
	{
		return new TeamsLoggerServiceProvider("logname", 0);
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
			[ 'Monolog.Handlers.TeamsHandler', HtmlFormattedTeamsLogHandler::class ]
		);
	}
}
