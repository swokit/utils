<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-12
 * Time: 14:33
 */

namespace SwoKit\Util;

use Swoole\Channel;
use Toolkit\PhpUtil\PhpHelper;
use Swoole\Coroutine as SwCo;

/**
 * Class Coroutine
 * @package SwoKit\Util
 */
class Coroutine
{
    /**
     * the coroutine id map
     * @var array
     * [
     *  child id => top id,
     *  child id => top id,
     *  ... ...
     * ]
     */
    private static $idMap = [];

    /**
     * get current coroutine id
     * @return int
     */
    public static function id(): int
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return -1;
        }

        return SwCo::getuid();
    }

    /**
     * get top coroutine id
     * @return int
     */
    public static function tid(): int
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return -1;
        }

        $id = SwCo::getuid();

        return self::$idMap[$id] ?? $id;
    }

    /**
     * @param callable $cb
     * @return bool
     */
    public static function go(callable $cb): bool
    {
        return self::create($cb);
    }

    /**
     * create a child coroutine
     * @param callable $cb
     * @return bool
     */
    public static function create(callable $cb): bool
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return false;
        }

        $tid = self::tid();

        return SwCo::create(function() use($cb, $tid) {
            $id = SwCo::getuid();
            self::$idMap[$id] = $tid;

            PhpHelper::call($cb);
        });
    }

    /**
     * @param callable $cb
     * @return mixed
     */
    public static function await(callable $cb)
    {
        // $ch = new Channel(0);
        // $tid = self::tid();
        //
        // SwCo::create(function () use($cb, $ch, $tid) {
        //     $ret = $cb();
        //     $ch->push($ret);
        // });
    }

    /**
     * @param int|float $seconds
     */
    public static function sleep($seconds)
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return;
        }

        SwCo::sleep($seconds);
    }

    /**
     * 挂起当前协程
     * @param string $coId
     */
    public static function suspend($coId)
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return;
        }

        SwCo::suspend($coId);
    }

    /**
     * 恢复某个协程，使其继续运行。
     * @param string $coId
     */
    public static function resume($coId)
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return;
        }

        SwCo::resume($coId);
    }
}
