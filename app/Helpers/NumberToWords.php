<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        0 => '', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
        6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh',
        11 => 'sebelas'
    ];

    public static function convert($number)
    {
        if ($number == 0) return 'nol';
        
        return self::convertGroup($number);
    }

    private static function convertGroup($number)
    {
        if ($number < 12) {
            return self::$ones[$number];
        } elseif ($number < 20) {
            return self::$ones[$number - 10] . ' belas';
        } elseif ($number < 100) {
            $tens = intval($number / 10);
            $ones = $number % 10;
            return self::$ones[$tens] . ' puluh' . (($ones > 0) ? ' ' . self::$ones[$ones] : '');
        } elseif ($number < 200) {
            $remainder = $number - 100;
            return 'seratus' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } elseif ($number < 1000) {
            $hundreds = intval($number / 100);
            $remainder = $number % 100;
            return self::$ones[$hundreds] . ' ratus' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } elseif ($number < 2000) {
            $remainder = $number - 1000;
            return 'seribu' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } elseif ($number < 1000000) {
            $thousands = intval($number / 1000);
            $remainder = $number % 1000;
            return self::convertGroup($thousands) . ' ribu' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } elseif ($number < 1000000000) {
            $millions = intval($number / 1000000);
            $remainder = $number % 1000000;
            return self::convertGroup($millions) . ' juta' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } elseif ($number < 1000000000000) {
            $billions = intval($number / 1000000000);
            $remainder = $number % 1000000000;
            return self::convertGroup($billions) . ' miliar' . (($remainder > 0) ? ' ' . self::convertGroup($remainder) : '');
        } else {
            return 'angka terlalu besar';
        }
    }
}