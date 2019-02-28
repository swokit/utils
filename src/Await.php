<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-12-30
 * Time: 12:52
 */

namespace Swokit\Util;

use Swoole\Coroutine\Channel;

/**
 * Class Await
 * @package Swokit\Util
 */
class Await
{
    /**
     * @param callable $fn
     * @param float    $timeout
     * @return mixed
     */
    public static function run(callable $fn, float $timeout = 2)
    {
        $chan = new Channel(1);

        go(function () use ($fn, $chan) {
            $ret = $fn();
            $chan->push($ret);
        });

        // var_dump(\Co::getuid());
        $result = $chan->pop($timeout);

        $chan->close();
        return $result;
    }

    /**
     * @param callable[] $fns
     * @param float $timeout
     * @return array
     */
    public static function multi(array $fns, float $timeout = 2): array
    {
        $len = \count($fns);
        $chan = new Channel($len);

        foreach ($fns as $fn) {
            go(function () use ($fn, $chan) {
                $ret = $fn();
                $chan->push($ret);
            });
        }

        $results = [];

        for ($i = 0; $i < $len; $i++) {
            $results[] = $chan->pop($timeout);
        }

        $chan->close();
        return $results;
    }
}
