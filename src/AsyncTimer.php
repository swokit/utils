<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-12-30
 * Time: 13:07
 */

namespace Swokit\Util;

use Swoole\Timer;

/**
 * Class SwooleTimer
 * @package Swokit\Util
 */
class AsyncTimer
{
    /**
     * @var array [tid => info]
     */
    private static $timers = [];

    /**
     * add a interval timer
     * @param float    $ms
     * @param callable $callback
     * @param mixed    $params
     * @return mixed
     */
    public static function tick(float $ms, callable $callback, $params = null)
    {
        $tid = Timer::tick($ms, $callback, $params);

        // save meta info
        self::$timers[$tid] = [
            'type' => 'tick',
            'ctime' => \time(), // add time.
            'interval' => $ms,
        ];

        return $tid;
    }

    /**
     * add a after timer
     * @param float    $ms
     * @param callable $callback
     * @param mixed    $params
     * @return mixed
     */
    public static function after(float $ms, callable $callback, $params = null)
    {
        $tid = Timer::after($ms, $callback, $params);

        // save meta info
        self::$timers[$tid] = [
            'type' => 'after',
            'ctime' => \time(), // add time.
            'interval' => $ms,
        ];

        return $tid;
    }

    /**
     * @param int $tid
     * @return bool
     */
    public static function clear(int $tid): bool
    {
        if (isset(self::$timers[$tid])) {
            unset(self::$timers[$tid]);
        }

        return Timer::clear($tid);
    }

    public static function clearAll(): void
    {
        foreach (self::$timers as $id => $info) {
            Timer::clear($id);
        }

        self::$timers = [];
    }

    /**
     * @return int
     */
    public static function count(): int
    {
        return \count(self::$timers);
    }

    /**
     * @return array
     */
    public static function getTimers(): array
    {
        return self::$timers;
    }
}
