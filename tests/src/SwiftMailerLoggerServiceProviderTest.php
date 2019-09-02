<?php
namespace tests;

use Germania\Logger\SwiftMailerLoggerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Handler\FingersCrossedHandler;
use Germania\Mailer\MailerServiceProvider;

class SwiftMailerLoggerServiceProviderTest extends \PHPUnit\Framework\TestCase
{	

	public function testInstantiation()
	{
		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );
		$this->assertInstanceOf( ServiceProviderInterface::class, $sut);
	}


	public function createSut()
	{
		$loglevel  = 100;
		return new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );
	}




	public function testMonologHandlers( )
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


		$container->register( $sut );

		$result = $container['Monolog.Handlers'];
		$this->assertInternalType("array", $result);
	}




	public function testExceptionOnMissingSwiftMailer( )
	{

		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );

		$container = new Container;
		$this->expectException( \Exception::class );
		$container->register( $sut );
	}


	public function testExceptionOnMissingSwiftMailerHtmlMessage( )
	{

		$loglevel  = 100;
		$sut = new SwiftMailerLoggerServiceProvider($loglevel, $loglevel );

		$container = new Container;
		$container['SwiftMailer'] = true;
		$this->expectException( \Exception::class );
		$container->register( $sut );
	}


}