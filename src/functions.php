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
