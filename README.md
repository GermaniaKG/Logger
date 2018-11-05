# Germania KG · Logger

**Default logging solution for Germania KG's websites:**
**Pimple Service Provider for Logging with Monolog.**




## Installation

```bash
$ composer require germania-kg/logger:^2.0
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/logger": "^2.0"
}
```

## Setup


```php
<?php
use Germania\Logger\LoggerServiceProvider;

// Have your Pimple or Slim3 Container at hand
$dic = new \Pimple\Container;

$log_name     = "My App";
$anonymize_ip = true;
$server_data  = $_SERVER;

$dic->register( new LoggerServiceProvider(
  $log_name,
  $server_data,
  $anonymize_ip
));


```

### Services

```php
// This Monolog Logger instance is your PSR-3 Logger
$dic['Logger']
  
// Monolog handlers array; 
// Empty per default; will be filled by one or more 
// of the specialised Service Providers below.
$dic['Logger.Handlers']
  
// Monolog Processors array;
// Default: just Monolog's "WebProcessor" with "ip", "method" and "url"
$dic['Logger.Processors']
 
```

### Usage:

```php
$logger = $dic['Logger'];

$logger->info("Hooray!");
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

This service requires service definitions for **SwiftMailer** and **SwiftMailer.HtmlMessage**

```php
<?php
use Germania\Logger\SwiftMailerLoggerServiceProvider;
use Monolog\Logger;

$dic->register( 
  new SwiftMailerLoggerServiceProvider( Logger::WARNING ) 
);
```



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



## Development

```bash
$ git clone https://github.com/GermaniaKG/Logger.git
$ cd Logger
$ composer install
```



## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. 
Run [PhpUnit](https://phpunit.de/) like this:

```bash
$ vendor/bin/phpunit
```
