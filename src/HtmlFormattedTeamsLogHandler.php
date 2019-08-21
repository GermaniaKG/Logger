<?php
namespace Germania\Logger;

use CMDISP\MonologMicrosoftTeams\TeamsLogHandler;
use CMDISP\MonologMicrosoftTeams\TeamsMessage;

use Monolog\Logger;

/**
 * TeamsLogHandler extension 
 * Additionally renders the _formatted_ Logger message
 */

class HtmlFormattedTeamsLogHandler extends TeamsLogHandler
{

    /**
     * (Make this private member accessible in this class)
     * @var array
     */
    private static $levelColors = [
        Logger::DEBUG => '0080FF',
        Logger::INFO => '0080FF',
        Logger::NOTICE => '0080FF',
        Logger::WARNING => 'FF8000',
        Logger::ERROR => 'FF0000',
        Logger::CRITICAL => 'FF0000',
        Logger::ALERT => 'FF0000',
        Logger::EMERGENCY => 'FF0000',
    ];


    /**
     * Uses the _formatted_ entry rather than the orginal.
     * @inheritDoc
     */
    protected function getMessage(array $record)
    {
        return new TeamsMessage([
            'title' => $record['level_name'] . ': ' . $record['message'],
            'text' => $record['formatted'],

            // This was the original:
            // 'text' => $record['level_name'] . ': ' . $record['message'],

            'themeColor' => self::$levelColors[$record['level']] ?? self::$levelColors[$this->level],
        ]);
    }

}