<img src="https://static.germania-kg.com/logos/ga-logo-2016-web.svgz" width="250px">

------



# Germania KG · Logger

**Default logging solution for our websites:**
**Pimple Service Provider for Logging with Monolog 1 and 2.**

[![Packagist](https://img.shields.io/packagist/v/germania-kg/logger.svg?style=flat)](https://packagist.org/packages/germania-kg/logger)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/logger.svg)](https://packagist.org/packages/germania-kg/logger)
[![Build Status](https://img.shields.io/travis/GermaniaKG/Logger.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/Logger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/build-status/master)



## Installation with Composer

The major release 4 requires PHP 7.2+. The older release 3 supports Monolog 2.

```bash
$ composer require germania-kg/logger
```


## Setup

Class **LoggerServiceProvider** is a Pimple *ServiceProviderInterface* and can be registered to any Pimple DI container. Its constructor requires the *App or Logger* name. – Optionally, the `$_SERVER` context may pe passed. An optional third parameter turns on IP address anonymizing:


```php
<?php
use Germania\Logger\LoggerServiceProvider;

// Have your Pimple or Slim3 Container at hand
$dic = new \Pimple\Container;
$dic->register( new LoggerServiceProvider( "My App" );
               
// Alternatively, pass custom server data environment,
// and/or disable IP address anonymization               
$dic->register( new LoggerServiceProvider( "My App", $_SERVER, false ));
```

### Services provided

This ***Monolog Logger*** instance is your PSR-3 Logger:

```php
<?php
use Psr\Log\LoggerInterface;
use Monolog\Logger as MonologLogger;

// These are equal and refer to the same instance:
$logger = $dic[LoggerInterface::class];
$logger = $dic[MonologLogger::class];

// Deprecated service names
$logger = $dic['Monolog.Psr3Logger'];
$logger = $dic['Logger'];

echo get_class($logger);
// Monolog\Logger
```

This ***Monolog Handlers*** array is empty per default; it will be filled by one or more of the specialised *Service Providers* below. 

Unless you want to add other handlers than those configured by the specialised Service providers you won't need to use these. 

```php
$handlers = $dic['Monolog.Handlers'];
print_r($handlers); // Array ...
```

This ***Monolog Processors*** array contains per default just Monolog's *WebProcessor* with `ip`, `method` and `url` extra context variables.

Unless you want to add other processors than those configured by the specialised Service providers you won't need to use these. 

```php
$processors = $dic['Monolog.Processors'];
print_r($processors); // Array ...
```



### Adding processors to handlers

*Monolog handlers* are provided by Pimple services. Grab the handler instance and add another *Monolog Processor* by extending the service definition:

```php
$dic->extend( \Monolog\Handler\RotatingFileHandler::class, function($handler, $dic) {
  $handler->pushProcessor( new \Monolog\Processor\IntrospectionProcessor );
  return $handler;
});
```



## Specialised Service Providers

### Log to Logfile

Class **FileLoggerServiceProvider** requires a *logfile path*. Optionally, you may pass a custom maximum *number of logfiles* (default: 30).

To set the loglevel, pass *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). Default is `Monolog\Logger::DEBUG`.

```php
<?php
use Germania\Logger\FileLoggerServiceProvider;
  
$dic->register( new FileLoggerServiceProvider( "log/app.log" ));
$dic->register( new FileLoggerServiceProvider( "log/app.log", 30, \Monolog\Logger::DEBUG));
```

**Retrieve the Monolog handler**

This handler is an instance of `\Monolog\Handler\RotatingFileHandler`

```php
$handler = $dic['Monolog.Handlers.RotatingFileHandler'];
$handler = $dic[\Monolog\Handler\RotatingFileHandler::class];
```



### Log to StdErr (stream)

Class **StreamLoggerServiceProvider** accepts optional parameters for an *output stream* (default: `php://stderr`) and a loglevel, either *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). Default is `Monolog\Logger::DEBUG`.



```php
<?php
use Germania\Logger\StreamLoggerServiceProvider;

$dic->register( new StreamLoggerServiceProvider );
$dic->register( new StreamLoggerServiceProvider("php://stderr", \Monolog\Logger::WARNING) );
```

**Retrieve the Monolog handler**

This handler is an instance of `\Monolog\Handler\StreamHandler`

```php
$handler = $dic['Monolog.Handlers.StreamHandler'];
$handler = $dic[\Monolog\Handler\StreamHandler::class];
```



### Log using SwiftMailer

This service requires service definitions for **SwiftMailer** and **SwiftMailer.HtmlMessage**. Germania KG's **[germania-kg/mailer](https://github.com/germaniaKG/Mailer)** will provide those.

```bash
$ composer require germania-kg/mailer
```

Class **SwiftMailerLoggerServiceProvider** accepts optional parameters for *outer log level* and *inner loglevel,* both either *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). 


The *outer loglevel* (default: `Monolog\Logger::WARNING`) will trigger Monolog's [FingersCrossedHandler](https://github.com/Seldaek/monolog/blob/main/src/Monolog/Handler/FingersCrossedHandler.php) which in turn uses Monolog's [BufferHandler](https://github.com/Seldaek/monolog/blob/main/src/Monolog/Handler/BufferHandler.php) to send an log messages digest using Monolog's [SwiftMailerHandler.](https://github.com/Seldaek/monolog/blob/main/src/Monolog/Handler/SwiftMailerHandler.php) Any log message in the email sent will be of *inner loglevel* upwards (default: `Monolog\Logger::DEBUG`)

```php
<?php
use Germania\Logger\SwiftMailerLoggerServiceProvider;

$dic->register( new SwiftMailerLoggerServiceProvider );
$dic->register( new SwiftMailerLoggerServiceProvider( \Monolog\Logger::WARNING ));
```

**Retrieve the Monolog handler**

Despite its name, the handler is actually an instance of `Monolog\Handler\FingersCrossedHandler` 
which wraps an instance of `Monolog\Handler\BufferHandler`
which wraps an instance of `Monolog\Handler\SwiftMailerHandler`

```php
$handler = $dic['Monolog.Handlers.SwiftMailerHandler'];
$handler = $dic[\Monolog\Handler\SwiftMailerHandler::class];
```



### Log using CLImate Logger

This requires **[CLImate](http://climate.thephpleague.com/)**, available with Composer: **[league/climate](https://github.com/thephpleague/climate)**

```bash
$ composer require league/climate
```
Class **ClimateLoggerServiceProvider** requires a *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). 

```php
<?php
use Germania\Logger\ClimateLoggerServiceProvider;

$dic->register( new ClimateLoggerServiceProvider( \Monolog\Logger::DEBUG ));
```

**Retrieve the Monolog handler**

N.B. This is actually a `Monolog\Handler\PsrHandler`instance which wraps a Climate Logger `League\CLImate\Logger`

```php
$handler = $dic['Climate.PsrLogger.MonologHandler'];
```



### Log using BrowserConsole Logger

Class **BrowserConsoleLoggerServiceProvider** optionally accepts a *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). If left out or set to `null`, logging to browser console will be skipped.

```php
<?php
use Germania\Logger\BrowserConsoleLoggerServiceProvider;

$dic->register( new BrowserConsoleLoggerServiceProvider );
$dic->register( new BrowserConsoleLoggerServiceProvider( \Monolog\Logger::INFO ));
```

**Retrieve the Monolog handler**

The handler is an instance of `Monolog\Handler\BrowserConsoleHandler`

```php
$handler = $dic['Monolog.Handlers.BrowserConsoleHandler'];
$handler = $dic[\Monolog\Handler\BrowserConsoleHandler::class];
```



### Log to Microsoft Teams

**Sends nicely formatted log messages to *Microsoft Teams* using Monolog's [*HtmlFormatter*](https://github.com/Seldaek/monolog/blob/main/src/Monolog/Formatter/HtmlFormatter.php).**

This requires CMDISP's **[monolog-microsoft-teams](https://github.com/cmdisp/monolog-microsoft-teams)** package, available via Composer: **[cmdisp/monolog-microsoft-teams](cmdisp/monolog-microsoft-teams)**. 

```bash
$ composer require cmdisp/monolog-microsoft-teams "^1.2"
```

Class **TeamsLoggerServiceProvider** requires a *[Incoming Webhook URL](https://docs.microsoft.com/de-de/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook)* string, and optionally a *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). Registering this ServiceProvider to a Pimple DI container will silently skip if the Webhook URL is empty.

```php
<?php
use Germania\Logger\TeamsLoggerServiceProvider;
use Monolog\Logger;

$incoming_webhook_url="https://outlook.office.com/webhook/many-many-letters";

$dic->register( new TeamsLoggerServiceProvider( $incoming_webhook_url ));
$dic->register( new TeamsLoggerServiceProvider( $incoming_webhook_url, \Monolog\Logger::NOTICE ));
```

#### Deprecation notice

Class `Germania\Logger\HtmlFormattedTeamsLogHandler` was an extension of the `CMDISP\MonologMicrosoftTeams\TeamsLogHandler` class and provided better log message formatting. As of the v1.1 release of CMDISP's **[monolog-microsoft-teams](https://github.com/cmdisp/monolog-microsoft-teams)** package, this extension is not needed any longer and will be removed as of major release 5.

**Retrieve the Monolog handler**

The handler is an instance of `\CMDISP\MonologMicrosoftTeams\TeamsLogHandler`

```php
$handler = $dic['Monolog.Handlers.TeamsHandler'];
$handler = $dic[\CMDISP\MonologMicrosoftTeams\TeamsLogHandler::class];
```



### Log to Slack channel

Class **SlackLoggerServiceProvider** requires *Slack token*, *channel*, and *username*. It optionally accepts a *Monolog Loglevel constant* or *PSR-3 LogLevel* (e.g.  `Monolog\Logger::DEBUG` or `\Psr\Log\LogLevel::INFO`). 

For more information on using Slack as Logger, see these links:

- https://blog.tschelhas.de/symfony/slack-als-logger-fuer-symfony-nutzen/
- https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SlackHandler.php

```php
<?php
use Germania\Logger\SlackLoggerServiceProvider;

$dic->register( new SlackLoggerServiceProvider(
  $slack_token,
  $slack_channel,
  $slack_username,
  \Monolog\Logger::CRITICAL
));


```

**Retrieve the Monolog handler**

The handler is an instance of `\Monolog\Handler\SlackHandler`

```php
$handler = $dic['Monolog.Handlers.SlackHandler'];
$handler = $dic[\Monolog\Handler\SlackHandler::class];
```



---



## Usage Example

```php
<?php
use Germania\Logger\LoggerServiceProvider;
use Germania\Logger\FileLoggerServiceProvider;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

// 1. Basic setup
$log_name     = "My App";
$anonymize_ip = true;
$server_data  = $_SERVER;

$dic->register( new LoggerServiceProvider(
  $log_name,
  $server_data,
  $anonymize_ip
));


// 2. The 'LoggerServiceProvider' alone won't do anything.
//    So, adding a specialized Service Provider is needed:
$max_files_count = 30;
$dic->register( new FileLoggerServiceProvider("log/app.log", 30, Monolog::DEBUG ));
$dic->register( new FileLoggerServiceProvider("log/app.log", 30, LogLevel::DEBUG ));


// 3. Now you can grab your PSR-3 Logger:
$logger = $dic[LoggerInterface::class];
$logger->info("Hooray!");
```



## Development and Unit tests

```bash
$ git clone https://github.com/GermaniaKG/Logger.git
$ cd Logger
$ composer install
```

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```

