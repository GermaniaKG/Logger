# Germania KG · Logger

**Default logging solution for Germania KG's websites:**
**Pimple Service Provider for Logging with Monolog 1 and 2.**


[![Packagist](https://img.shields.io/packagist/v/germania-kg/logger.svg?style=flat)](https://packagist.org/packages/germania-kg/logger)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/logger.svg)](https://packagist.org/packages/germania-kg/logger)
[![Build Status](https://img.shields.io/travis/GermaniaKG/Logger.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/Logger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/Logger/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/Logger/build-status/master)



## Installation with Composer

The major release 4 requires PHP 7.2 and the older release 3 supports Monolog 2.

```bash
$ composer require germania-kg/logger
```
## Setup


```php
<?php
use Germania\Logger\LoggerServiceProvider;

// Have your Pimple or Slim3 Container at hand
$dic = new \Pimple\Container;
$dic->register( new LoggerServiceProvider( "My App" );
               
// Alternatively, pass custom server data environment,
// disable IP address anonymization               
$dic->register( new LoggerServiceProvider(
  "My App",
  $_SERVER,
  false
));
```

### Services provided

```php
// This Monolog Logger instance is your PSR-3 Logger
$dic['Logger']
  
// Monolog handlers array; 
// Empty per default; will be filled by one or more 
// of the specialised Service Providers below.
$dic['Monolog.Handlers']
  
// Monolog Processors array;
// Default: just Monolog's "WebProcessor" with "ip", "method" and "url"
$dic['Monolog.Processors']
 
```



## Specialised Service Providers

### Log to logfile

```php
<?php
use Germania\Logger\FileLoggerServiceProvider;
use Monolog\Logger;
  
$max_files_count = 30;
$dic->register( new FileLoggerServiceProvider(
  "var/log/app.log",
  $max_files_count,
  Logger::DEBUG
));
```



### Log to StdErr (stream)

```php
<?php
use Germania\Logger\StreamLoggerServiceProvider;
use Monolog\Logger;

$dic->register( new StreamLoggerServiceProvider );

// Custom values, here the defaults:
$dic->register( new StreamLoggerServiceProvider("php://stderr", Logger::WARNING) );

```



### Log using SwiftMailer

This service requires service definitions for **SwiftMailer** and **SwiftMailer.HtmlMessage**. Germania KG's **[germania-kg/mailer](https://github.com/germaniaKG/Mailer)** will provide those.

```php
<?php
use Germania\Logger\SwiftMailerLoggerServiceProvider;
use Monolog\Logger;

$dic->register( 
  new SwiftMailerLoggerServiceProvider( Logger::WARNING ) 
);
```



### Log using CLImate Logger

This requires **[CLImate](http://climate.thephpleague.com/)**, available with Composer: **[league/climate](https://github.com/thephpleague/climate)**

```bash
$ composer require league/climate
```

**Usage:**

```php
<?php
use Germania\Logger\ClimateLoggerServiceProvider;
use Monolog\Logger;

$dic->register( 
  new ClimateLoggerServiceProvider( Logger::INFO ) 
);
```



### Log to Microsoft Teams

This requires CMDISP's **[monolog-microsoft-teams](https://github.com/cmdisp/monolog-microsoft-teams)** package, available via Composer: **[cmdisp/monolog-microsoft-teams](cmdisp/monolog-microsoft-teams)**. 

```bash
composer require cmdisp/monolog-microsoft-teams "^1.1"
```

**Usage:**

```php
<?php
use Germania\Logger\TeamsLoggerServiceProvider;
use Monolog\Logger;

$incoming_webhook_url="https://outlook.office.com/webhook/many-many-letters";

$dic->register( new TeamsLoggerServiceProvider(
  $incoming_webhook_url,
  Logger::NOTICE
));
```



#### Deprecated: HtmlFormattedTeamsLogHandler

The `Germania\Logger\HtmlFormattedTeamsLogHandler` was an extension of the `CMDISP\MonologMicrosoftTeams\TeamsLogHandler` class and provided better log message formatting. As of the v1.1 release of CMDISP's **[monolog-microsoft-teams](https://github.com/cmdisp/monolog-microsoft-teams)** package, this extension is not needed any longer and will be removed. 



### Log to Slack channel

For more information, see these links:

https://blog.tschelhas.de/symfony/slack-als-logger-fuer-symfony-nutzen/

https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/SlackHandler.php

```php
<?php
use Germania\Logger\SlackLoggerServiceProvider;
use Monolog\Logger;

$dic->register( new SlackLoggerServiceProvider(
  $slack_token,
  $slack_channel,
  $slack_username,
  Logger::CRITICAL
));


```

## Example

```php
<?php
use Germania\Logger\LoggerServiceProvider;
use Germania\Logger\FileLoggerServiceProvider;
use Monolog\Logger;

// 
// 1. Basic setup
//
$log_name     = "My App";
$anonymize_ip = true;
$server_data  = $_SERVER;

$dic->register( new LoggerServiceProvider(
  $log_name,
  $server_data,
  $anonymize_ip
));


//
// 2. The 'Logger' service won't do anything right here.
// Adding a specialized Service Provider is needed:
//
$max_files_count = 30;
$dic->register( new FileLoggerServiceProvider(
  "var/log/app.log",
  $max_files_count,
  Logger::DEBUG
));


// 
// 3. Now you can grab your PSR-3 Logger:
//
$logger = $dic['Logger'];
$logger->info("Hooray!");
```



## Development

```bash
$ git clone https://github.com/GermaniaKG/Logger.git
$ cd Logger
$ composer install
```

## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```
