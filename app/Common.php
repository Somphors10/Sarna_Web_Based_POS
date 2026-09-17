<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('bcmul')) {
    function bcmul($num1, $num2, $scale = 2): string
    {
        return (string) round(((float) $num1) * ((float) $num2), (int) $scale);
    }
}

if (!function_exists('bcdiv')) {
    function bcdiv($num1, $num2, $scale = 2): string
    {
        $divisor = (float) $num2;
        if ($divisor == 0.0) {
            return '0';
        }

        return (string) round(((float) $num1) / $divisor, (int) $scale);
    }
}

if (!function_exists('bcadd')) {
    function bcadd($num1, $num2, $scale = 2): string
    {
        return (string) round(((float) $num1) + ((float) $num2), (int) $scale);
    }
}

if (!function_exists('bcsub')) {
    function bcsub($num1, $num2, $scale = 2): string
    {
        return (string) round(((float) $num1) - ((float) $num2), (int) $scale);
    }
}
