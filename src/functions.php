<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/6/16 0016
 * Time: 16:03
 */

if (!function_exists('sco')) {
    /**
     * @param callable $cb
     * @return bool
     */
    function sco(callable $cb)
    {
        return \Swokit\Util\Coroutine::create($cb);
    }
}

// if (!function_exists('await')) {
//     function await(callable $cb)
//     {
//         return \Swokit\Util\Coroutine::await($cb);
//     }
// }

function await(Closure $fn)
{
    $ch = new \Swoole\Coroutine\Channel(1);

    go(function () use ($fn, $ch) {
        $ret = $fn();
        $ch->push($ret);
    });

    // var_dump(\Co::getuid());
    return $ch->pop();
}

function await_multi(float $timeout, Closure ...$fns)
{
    $len = \count($fns);
    $chan = new \Swoole\Coroutine\Channel($len);

    foreach ($fns as $fn) {
        go(function () use ($fn, $chan) {
            $ret = $fn();
            $chan->push($ret);
        });
    }

    $results = [];

    for ($i = 0; $i < $len; $i++) {
        $results[] = $chan->pop();
    }

    return $results;
}
