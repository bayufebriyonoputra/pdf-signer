<?php
// File: app/Helpers/DateHelper.php

use Carbon\Carbon;

/**
 * Menghitung tanggal dengan menambahkan TOP (hari) ke tanggal awal
 * dan memastikan hasil selalu jatuh di hari Rabu
 *
 * @param string|Carbon $tanggal - Tanggal awal
 * @param int $top - Jumlah hari yang ditambahkan
 * @return Carbon - Tanggal hasil yang selalu jatuh di hari Rabu
 */
function calculateWednesdayDate($tanggal, $top)
{
    // Pastikan tanggal dalam format Carbon
    $date = Carbon::parse($tanggal);

    // Tambahkan TOP (hari) ke tanggal awal
    $resultDate = $date->addDays($top);

    // Dapatkan hari dalam seminggu (1=Senin, 2=Selasa, 3=Rabu, dst)
    $dayOfWeek = $resultDate->dayOfWeek;

    // Jika hari Minggu, ubah ke 7 untuk konsistensi
    if ($dayOfWeek == 0) {
        $dayOfWeek = 7;
    }

    // Logika penyesuaian ke hari Rabu (3)
    switch ($dayOfWeek) {
        case 1: // Senin - majukan 2 hari ke Rabu
            $resultDate->addDays(2);
            break;
        case 2: // Selasa - majukan 1 hari ke Rabu
            $resultDate->addDays(1);
            break;
        case 3: // Rabu - tetap
            // Tidak perlu perubahan
            break;
        case 4: // Kamis - mundur 1 hari ke Rabu
            $resultDate->subDays(1);
            break;
        case 5: // Jumat - mundur 2 hari ke Rabu
            $resultDate->subDays(2);
            break;
        case 6: // Sabtu - mundur 3 hari ke Rabu
            $resultDate->subDays(3);
            break;
        case 7: // Minggu - mundur 4 hari ke Rabu
            $resultDate->subDays(4);
            break;
    }

    return $resultDate;
}
