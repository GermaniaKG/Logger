<?php
namespace tests;

use Germania\Logger\LoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\AbstractHandler;
use Psr\Log\LoggerInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class LoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

	/**
	 * @dataProvider provideCtorArgs
	 */
	public function testInstantiation( $logname, $server, $anonymize )
	{

		$sut = new LoggerServiceProvider($logname, $server, $anonymize);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}

	public function provideCtorArgs()
	{
		return array(
			[ "logname", array(), true ],
			[ "logname", array(), false ],
			[ "logname", array( 'REMOTE_ADDR' => '127.0.0.1'), false ],
			[ "logname", array( 'REMOTE_ADDR' => '127.0.0.1'), true ]

		);
	}


	public function createSut()
	{
		return new LoggerServiceProvider("logname", array(), true);
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
			[ 'Monolog.Handlers', 'array' ],
			[ 'Monolog.Processors', 'array' ]
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
			[ 'Logger',             LoggerInterface::class ],
			[ 'Monolog.Psr3Logger', LoggerInterface::class ],
            [ LoggerInterface::class, LoggerInterface::class ]
		);
	}



}
