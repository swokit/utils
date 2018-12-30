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

function await(callable $fn, float $timeout = 2)
{
    return \Swokit\Util\Await::run($fn, $timeout);
}

function await_multi(array $fns, float $timeout = 2)
{
    return \Swokit\Util\Await::multi($fns, $timeout);
}
