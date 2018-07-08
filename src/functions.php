<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/6/16 0016
 * Time: 16:03
 */

if (!function_exists('co')) {
    function co(callable $cb)
    {
        return \SwooleKit\Util\Coroutine::create($cb);
    }
}

// if (!function_exists('await')) {
//     function await(callable $cb)
//     {
//         return \SwooleKit\Util\Coroutine::await($cb);
//     }
// }

function await(Closure $fn) {
    $ch = new chan(1);

    go(function () use($fn, $ch) {
        $ret = $fn();
        $ch->push($ret);
    });

    // var_dump(\Co::getuid());
    return $ch->pop();
}

function await_multi(Closure ...$fns) {
    $len = count($fns);
    $ch = new chan($len);

    foreach ($fns as $fn) {
        go(function () use($fn, $ch) {
            $ret = $fn();
            $ch->push($ret);
        });
    }

    $results = [];

    for ($i = 0; $i < $len; $i++) {
        $results[] = $ch->pop();
    }

    return $results;
}
