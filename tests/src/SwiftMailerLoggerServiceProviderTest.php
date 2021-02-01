<?php
namespace tests;

use Germania\Logger\SwiftMailerLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Germania\Mailer\MailerServiceProvider;
use Prophecy\PhpUnit\ProphecyTrait;

class SwiftMailerLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;


    /**
     * @dataProvider provideVariousLogLevels
     */
	public function testInstantiation($loglevel) : void
	{
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );
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


	public function createSut() : SwiftMailerLoggerServiceProvider
	{
		$loglevel  = 100;
		return new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );
	}




	public function testMonologHandlers( ) : void
	{

		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );

		$container = new Container;

		// Use external package "germania-kg/mailer",
		// working around its code flaws
		$container->register( new MailerServiceProvider );
		$container->extend('Mailer.Config', function($default) {
			$default['to'] = [ 'me@test.com'];
			$default['from_mail'] = 'me@test.com';
			return $default;
		});

		$result = $container->register( $sut );
        $this->assertNotNull($result);
	}




	public function testExceptionOnMissingSwiftMailer( ) : void
	{

		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );

		$container = new Container;
		$this->expectException( \Exception::class );
		$container->register( $sut );
	}


	public function testExceptionOnMissingSwiftMailerHtmlMessage( ) : void
	{

		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );

		$container = new Container;
		$container['SwiftMailer'] = true;
		$this->expectException( \Exception::class );
		$container->register( $sut );
	}


}
