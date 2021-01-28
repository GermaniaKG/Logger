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
			[ 'Climate.PsrLogger.MonologHandler', PsrHandler::class ],
			[ 'Climate.PsrLogger', CLImateLogger::class ]
		);
	}
}
