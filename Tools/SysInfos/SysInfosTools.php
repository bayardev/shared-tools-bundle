<?php

namespace Bayard\Bundle\SharedToolsBundle\Tools\SysInfos;

/**
 * @author Pasquesi Massimiliano <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SysInfosTools
{
    public static function humanReadableBytes($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    public static function getMemoryUsage($real_usage = true, $human_readable = true)
    {
        return ($human_readable == true) ?
            self::humanReadableBytes(memory_get_usage($real_usage)) :
            memory_get_usage($real_usage);
    }

    public static function getMemoryPeakUsage($real_usage = true, $human_readable = true)
    {
        return ($human_readable == true) ?
            self::humanReadableBytes(memory_get_peak_usage($real_usage)) :
            memory_get_peak_usage($real_usage);
    }

    public static function getServerMemoryUsage()
    {
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2]/$mem[1]*100;

        return $memory_usage;
    }

    public static function getServerCpuUsage($full_load_average = false, $as_string = true)
    {
        $load = sys_getloadavg();

        if ($full_load_average == false) {
            return $load[0];
        }

        if ($as_string == false) {
            return $load;
        }

        return implode(",", $load);
    }

    public static function onRequestStart()
    {
        $dat = getrusage();
        define('PHP_TUSAGE', microtime(true));
        define('PHP_RUSAGE', $dat["ru_utime.tv_sec"]*1e6+$dat["ru_utime.tv_usec"]);
    }

    public static function getCpuUsage()
    {
        $dat = getrusage();
        $dat["ru_utime.tv_usec"] = ($dat["ru_utime.tv_sec"]*1e6 + $dat["ru_utime.tv_usec"]) - PHP_RUSAGE;
        $time = (microtime(true) - PHP_TUSAGE) * 1000000;

        // cpu per request
        if($time > 0) {
            $cpu = sprintf("%01.2f", ($dat["ru_utime.tv_usec"] / $time) * 100);
        } else {
            $cpu = '0.00';
        }

        return $cpu;
    }
}