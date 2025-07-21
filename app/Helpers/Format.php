<?php

use Carbon\Carbon;

if (!function_exists('rupiah')) {
    function rupiah($angka)
    {
        return 'Rp. ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('toRoman')) {

    function toRoman($number)
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $map[$number] ?? '';
    }
}

if (!function_exists('parseRandomDate')) {
    function parseRandomDate($dateString)
    {
        $formats = [
            'd-m-Y',    // 12-02-2025
            'm-d-Y',    // 02-12-2025
            'Y-m-d',    // 2025-02-12
            'd/m/Y',    // 12/02/2025
            'm/d/Y',    // 02/12/2025
            'Y/m/d',    // 2025/02/12
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            } catch (Exception $e) {
                continue;
            }
        }

        // Jika semua format gagal, coba Carbon::parse()
        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (Exception $e) {
            throw new InvalidArgumentException("Cannot parse date: " . $dateString);
        }
    }
}
