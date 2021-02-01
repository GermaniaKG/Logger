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
use Monolog\Handler\SwiftMailerHandler;

class SwiftMailerLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    use ProphecyTrait;


	public function testInstantiation() : SwiftMailerLoggerServiceProvider
	{
		$sut = new SwiftMailerLoggerServiceProvider(LogLevel::WARNING, LogLevel::INFO);
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        return $sut;
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




    public function provideServicesAndInterfaces() : array
    {
        return array(
            [ SwiftMailerHandler::class, SwiftMailerHandler::class ]
        );
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
