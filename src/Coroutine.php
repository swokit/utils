<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-12
 * Time: 14:33
 */

namespace Swokit\Util;

use Swoole\Coroutine as SwCoroutine;
use Toolkit\PhpUtil\PhpHelper;

/**
 * Class Coroutine
 * @package Swokit\Util
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

        return SwCoroutine::getuid();
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

        $id = SwCoroutine::getuid();

        return self::$idMap[$id] ?? $id;
    }

    /**
     * @param callable $cb
     * @return bool|int
     */
    public static function go(callable $cb)
    {
        return self::create($cb);
    }

    /**
     * create a child coroutine
     * @param callable $cb
     * @return bool|int success return CID, fail return false.
     */
    public static function create(callable $cb)
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return false;
        }

        $tid = self::tid();

        return SwCoroutine::create(function () use ($cb, $tid) {
            $id = SwCoroutine::getuid();

            self::$idMap[$id] = $tid;

            PhpHelper::call($cb);
        });
    }

    /**
     * @param int|float $seconds
     */
    public static function sleep($seconds)
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return;
        }

        SwCoroutine::sleep($seconds);
    }

    /**
     * 挂起当前协程
     */
    public static function suspend()
    {
        if (!ServerUtil::coroutineIsEnabled()) {
            return;
        }

        SwCoroutine::yield();
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

        SwCoroutine::resume($coId);
    }
}
