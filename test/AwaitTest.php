<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-12-30
 * Time: 14:06
 */

namespace Swokit\UtilTest;

use PHPUnit\Framework\TestCase;
use Swokit\Util\Await;

/**
 * Class AwaitTest
 * @package Swokit\UtilTest
 */
class AwaitTest extends TestCase
{
    protected function tearDown()
    {
        \swoole_event_wait();
    }

    public function testRun()
    {
        \go(function () {
            $ret = Await::run(function () {
                return 2;
            });

            $this->assertSame(2, $ret);
        });
    }

    public function testMulti()
    {
        \go(function () {
            $ret = Await::multi([
                function () {
                    return 2;
                },
                function () {
                    return 'OK';
                },
                function () {
                    return ['hi'];
                },
            ]);

            $this->assertCount(3, $ret);
            $this->assertSame([2, 'OK', ['hi']], $ret);
        });
    }
}
