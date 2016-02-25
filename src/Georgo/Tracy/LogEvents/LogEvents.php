<?php

/**
 * LogEvents - event debugger for Tracy with bar implementation
 * Copyright (c) 2016 Tomas Kopecny (https://tomask.info)
 */

namespace Georgo\Tracy;

use Tracy\IBarPanel;
use Tracy\Debugger;
use Tracy\ILogger;

class LogEvents implements IBarPanel
{
	private static $events = [];

	private static $icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABIFBMVEUAAACRim7h4uPe3+EDAwPQ0dOjpagVERgAAAU3KR4NDBKUl5umqKqdn6GUlZeWmJmdnp99fX5UVFRZWFd8e3jj5eZ8foCldw/p6eqmqa7Y1sxxc3agjGmZmpyLjI19fX97fH6SmKKNYy2HYC2goaKbnqV1dXeMe06ZeUKKYy+dbgB7VzB5h6aznml5entqamyenqB2fYpsTTJ/WxFwTSNxcnPLrmVUVF9wVxx2d3eMYydWV1dtbm57b042Iwm3gwA2NzWGhYVJKTk+PTwDBQPKyMHOz9HDxcfBw8W7vb/f4OHW19nV1ti3uLqtr7CEhYi6u7ygoqTc4OjR1NrV19nJys3AwcOXmZuTlZeSkpaGiYxkZWjWtmKSZy6UcCiWaB25f/joAAAARnRSTlMAAv78A/7+Mh0UEf79++bSv5A0Iwz+/v78+/r6+PTz8/Dv6+fj2dnY2NTTzsnJuK6sopyWk5GRiH54d19cUE5NSEMqHAwFKlh2dgAAAMhJREFUGNNNz9WOw0AMBdCZcFPcbZe3zMzMFG5SZvj/v+ikENUv1rFk2Rc8C0LXGBpCBn1v1oWa4Xf37pABL7Yp1J9WB/Bhpw2TptrW43x4aJe+BXJmpd7uZu3Sh8DLa4sYYnWPHBjGc7LCiaLaQiYcGL5SZE6YiBYz2iFyOE4uEdXZxqqVQTc9x0mOn6qUef/pizRB5fi74Cc//lT0XO3RDAHaX//hfCxeKyYug9tbplKHAY1TIJgsmFBC/ao+pWmGJYzc8DX2FT+NGsxmtW49AAAAAElFTkSuQmCC';
	private static $iconDebug = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAAAnNCSVQICFXsRgQAAAAJcEhZcwAAAG4AAABuASPlPqcAAAAZdEVYdFNvZnR3YXJlAHd3dy5pbmtzY2FwZS5vcmeb7jwaAAAAz0lEQVQYGQXBMSoAAABA0WcxUCwmKXICDmHCblGULKQsVotVysAqJqNwAFlcQAYZCRkoFqnvPYkh195tJkmSJBIrkiwkSZJE4lCS8yRJkkhcS/KTxIAZI0kkjiX5Tgy7lDdTicS2JK+JdUm2Eolpf5KnxI0kJ4kkzuTLbuJXkotEEhPmLDswEF+S7CeSxItkPp4lWUskiQvJTtxLPo0mksSsHx/GY9GVT3tJJEmMm4xhD04tGUwiSZLEquQxSSRJktiQHCWJJEkSg27dGUuSf37t3Kbt1vkmAAAAAElFTkSuQmCC';
	private static $iconInfo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAS1BMVEUAAAAAAAIBAAIAAAABAAICAAIAAAIAAAIBAAEAAAAAAAABAAIBAAIBAAEBAAEAAAIAAAAAAAABAAMBAAECAAIAAAMAAAMAAAABAAJY+EAaAAAAGHRSTlMAbvYi15x8abQzC+bksbB2LALFupdlXxaZT5yTAAAAgElEQVQY022PWQ4DIQxDzRbosA10y/1PWkXJx1Sa/GCeI8WGTKBe26AAG+djyjlF7/RfRHwclvNF/RM4mANwihV08T3VDKC4RM35FRIJI4nYzA95U0fLIg4DuaL+g4aeriANULyAFcnOTuaXnrVgZe+nBtPoyzqVm3JWf7Tatf4Pof0E/LYeaEgAAAAASUVORK5CYII=';
	private static $iconWarning = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAArAAAAKwBhgk01AAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAEHSURBVDiNndIxS8JRFAXw38vAwdWhNl0aAkebnZvCRgdX+wjugh9AMHBzFJwbdYlAaIn2psagwaHFeC0vsH9PyS5cuPfccw4X7g0xRrsihBAg7iEd7RGfYIVVqg8zwBCfKYc7WTHGX4kmNrhIuUEzy82IAx4w3cKmCQt/MehgjVOc4yzVa3T2GqCCV/RTf4/HVPfTrLLPYIAXlFO/xDLV5TQbZA1QwwfaW9gMs62+nTi1nMEci8JGI4wK2ALzHwZopVM1CuRrXBWwRuK2vh+0hCeMMxfpopvBx0lTgh7eUc0Q66hn8GrS9AKecYfbne+ajxtcwgTxnzkJMUYhhCqOD9xgE2N8+wLoQkqBXU8jzAAAAABJRU5ErkJggg==';
	private static $iconError = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAV1BMVEUAAAACAAIBAAIBAAIAAAIAAAABAAIBAAEBAAEAAAABAAIBAAIBAAMCAAICAAIAAAIAAAMAAAMAAAAAAAAAAAAAAAABAAIBAAIBAAEAAAAAAAAAAAABAALaZCeYAAAAHHRSTlMAiPjePwzAubQV89LFk4FvXUs4CQYB6+qtLigb4eGITQAAAG9JREFUGNNVjFkSREAQBWv0QtsZZiHvf05KE3T+ZUa8JyejlYQF/kn4QfH0EeA58vic+vYB3DtjuHyuQCSjms/QEwN99MlcwUxH6CB+QKfuvgB5AfBxe2hRao/SioQSZQ0oZZAG7gk0YkgwYl8JdgP2Mwwu2wUzAwAAAABJRU5ErkJggg==';
	private static $iconException = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAASFBMVEUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACrhKybAAAAF3RSTlMAtSX254NvBrCmDgPZzhLJwzLgxEQzHuuhiDoAAAB5SURBVBjTZU9bDsMwCDM0adesade9uP9NZ2CaJsVfsWMbgEOrlCJV8cU8WWCak4v9IPFvf6BH6d9e3exxbkwpKvU39n40KJ81GtYr9oblEi3FUkluJYXbArSegjinn6nDI1F60s/U00tzrDK/6t3HDosNqw/HDed/AJpXDFvBCFKcAAAAAElFTkSuQmCC';
	private static $iconCritical = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAHlBMVEUAAAABAAMBAAIBAAEBAAICAAIAAAIAAAABAAMBAAKZ/F0sAAAACXRSTlMAyPW154NvJcDzYszLAAAATElEQVQI12NgYCg1cQ5nAAI2zZkzJyUAGZYzgWAyUGAmGCQwFEEY6gyREMZUBstJgkCgOZnBcwZIa+cUOAMuBVcM1w43EGYFwlK4MwALhy59NASnAgAAAABJRU5ErkJggg==';

	private static $errorLevels = [
		ILogger::DEBUG => 'iconDebug',
		ILogger::INFO => 'iconInfo',
		ILogger::WARNING => 'iconWarning',
		ILogger::ERROR => 'iconError',
		ILogger::EXCEPTION => 'iconException',
		ILogger::CRITICAL => 'iconCritical'
	];

	private static $counts = [
		ILogger::DEBUG => 0,
		ILogger::INFO => 0,
		ILogger::WARNING => 0,
		ILogger::ERROR => 0,
		ILogger::EXCEPTION => 0,
		ILogger::CRITICAL => 0
	];

	public static function __contruct() {
		self::$startTime = self::getMicrotime();
	}

	public static function log($message, $priority = ILogger::INFO) {
		self::$events[] = [
			'message' => $message,
			'priority' => $priority,
			'time' => self::getMicrotime()
		];
		self::$counts[$priority]++;

		return Debugger::log('['.getmypid().'] ' .$message, $priority);
	}

	public function getTab() {
		$count = count(self::$events);
		if ($count) {
			$title = 'Events: '.$count;
		} else {
			$title = 'No events';
		}
		$output = ['<span title="'.$title.'">', '<img src="'. self::$icon .'"> ', $title, '</span>'];
		return join('', $output);
	}

	public function getPanel() {

		$output = ['<h1>Events</h1>', '<div class="tracy-inner">'];
		$count = count(self::$events);
		if ($count) {
			$output[] = '<p>';
			foreach (self::$counts as $priority => $priorityCount) {
				if ($priorityCount) {
					$output[] = '<strong>'. $priorityCount .'</strong>&times;<img src="'. self::${self::$errorLevels[$priority]} .'" title="'. htmlspecialchars($priority) .'" align="top"> &nbsp;';
				}
			}
			$output[] = '</p><table><tbody>';
			$lastEvent = 0;
			foreach (self::$events as $event) {
				$output[] = '<tr>';
				if($lastEvent == 0) {
					$output[] = '<td>&nbsp;</td>';	
					$lastEvent = $event['time'];
				} else {
					$delta = $event['time'] - $lastEvent;
					$output[] = sprintf('<td>%+.2fs</td>', $delta);
				}
				$priority = $event['priority'];
				$output[] = '<td><img src="'. self::${self::$errorLevels[$priority]} .'" title="'. htmlspecialchars($priority) .'"></td>';
				$output[] = '<td><pre class="dump">'.$event['message'].'</pre></td>';
				$output[] = '</tr>';
			}
			$output[] = '</tbody></table>';
		}
		else {
			$output[] = 'No events triggered.';
		}
		$output[] = '</div>';
		return join('', $output);
	}

	public function getId() {
		return get_class($this);
	}

    private static function getMicrotime() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}
