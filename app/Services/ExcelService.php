<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;

class ExcelService
{
    public function extract($file): ?string
    {
        try {
            $data = Excel::toArray([], $file);

            return json_encode($data);

        } catch (\Exception $e) {
            return null;
        }
    }

    public function extractTable($file): ?array
    {
        try {

            $sheets = Excel::toArray([], $file);

            return [
                'sheets_count' => count($sheets),

                // 🔥 limitar para evitar overflow
                'sheets' => array_map(function ($sheet) {
                    return array_slice($sheet, 0, 100); // máximo 100 filas
                }, $sheets),

                'rows_total' => array_sum(array_map('count', $sheets)),

                'headers' => $sheets[0][0] ?? null,
            ];

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}