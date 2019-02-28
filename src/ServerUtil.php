<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018-01-24
 * Time: 19:09
 */

namespace Swokit\Util;

use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Server;

/**
 * Class ServerHelper
 * @package Swokit\Util
 */
final class ServerUtil
{
    /**
     * @throws \RuntimeException
     */
    public static function checkRuntimeEnv(): void
    {
        if (PHP_SAPI !== 'cli') {
            throw new \RuntimeException('Server must run in the CLI mode.');
        }

        if (!\extension_loaded('swoole')) {
            throw new \RuntimeException("Run the server, extension 'swoole' is required!");
        }
    }

    /**
     * see Runtime Env
     */
    public static function getRuntimeEnv(): array
    {
        $yes  = '<info>√</info>';
        $no   = '<danger>X</danger>';
        $tips = '<danger>please disabled</danger>';

        return [
            'Php version is gt 7.1'       => version_compare(PHP_VERSION, '7.1') ? $yes : $no,
            'Swoole is installed'         => class_exists(Server::class, false) ? $yes : $no,
            'Swoole version is gt 2'      => version_compare(SWOOLE_VERSION, '2.0') ? $yes : $no,
            'Swoole Coroutine is enabled' => class_exists(Coroutine::class, false) ? $yes : $no,
            'XDebug extension exists'     => \extension_loaded('xdebug') ? $yes . "($tips)" : $no,
            'xProf extension exists'      => \extension_loaded('xprof') ? $yes . "($tips)" : $no,
        ];
    }

    /**
     * @return bool
     */
    public static function coroutineIsEnabled(): bool
    {
        return self::isSupportCoroutine();
    }

    /**
     * @return bool
     */
    public static function coIsEnabled(): bool
    {
        return self::isSupportCoroutine();
    }

    /**
     * @return bool
     */
    public static function isSupportCoroutine(): bool
    {
        return \class_exists(Coroutine::class, false);
    }

    /**
     * @return bool
     */
    public static function inCoroutine(): bool
    {
        if (self::isSupportCoroutine()) {
            return Coroutine::getuid() > 0;
        }

        return false;
    }

    /**
     * @param string $file
     * @param bool   $checkLive
     * @return int
     */
    public static function getPidFromFile(string $file, bool $checkLive = false): int
    {
        if ($file && \file_exists($file)) {
            $pid = (int)\file_get_contents($file);

            // check live
            if ($checkLive && self::isRunning($pid)) {
                return $pid;
            }

            \unlink($file);
        }

        return 0;
    }

    /**
     * @param int    $masterPid
     * @param string $pidFile
     * @return bool|int
     */
    public static function createPidFile(int $masterPid, string $pidFile)
    {
        if ($pidFile) {
            return \file_put_contents($pidFile, $masterPid);
        }

        return false;
    }

    /**
     * @param string $pidFile
     * @return bool
     */
    public static function removePidFile(string $pidFile): bool
    {
        if ($pidFile && \file_exists($pidFile)) {
            return \unlink($pidFile);
        }

        return false;
    }

    /**
     * @param int $pid
     * @return bool
     */
    public static function isRunning(int $pid): bool
    {
        return ($pid > 0) && Process::kill($pid, 0);
    }
}
