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

    public function extractTable2($file): ?array
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

public function extractTable($file): ?array
{
    try {

        $sheets = Excel::toArray([], $file);

        if (count($sheets) === 0) {
            return ['error' => 'El archivo Excel está vacío.'];
        }

        $sheet = $sheets[0];

        // eliminar filas completamente vacías
        $sheet = array_values(array_filter($sheet, function ($row) {
            return collect($row)
                ->filter(fn($cell) => $cell !== null && $cell !== '')
                ->isNotEmpty();
        }));

        // ===========================
        // 1. DETECTAR HEADERS
        // ===========================
        $headers = null;
        $headerIndex = null;

        foreach ($sheet as $i => $row) {

            if (collect($row)->filter(fn($c) => $c !== null && $c !== '')->isNotEmpty()) {

                $headers = array_map(fn($h) => trim($h ?? ''), $row);
                $headerIndex = $i;
                break;
            }
        }

        if (!$headers) {
            return ['error' => 'No se pudieron detectar encabezados.'];
        }

        // ===========================
        // 2. IDENTIFICAR COLUMNAS VÁLIDAS
        // ===========================
        $validIndexes = [];

        foreach ($headers as $i => $header) {
            if ($header !== '') {
                $validIndexes[] = $i;
            }
        }

        // reconstruir headers limpios
        $cleanHeaders = [];

        foreach ($validIndexes as $i) {
            $cleanHeaders[] = $headers[$i];
        }

        // ===========================
        // 3. CONSTRUIR ROWS LIMPIOS
        // ===========================
        $rows = [];

        for ($i = $headerIndex + 1; $i < count($sheet); $i++) {

            $row = $sheet[$i];

            $cleanRow = [];
            $hasData = false;

            foreach ($validIndexes as $colIndex) {

                $value = $row[$colIndex] ?? '';

                if ($value === null) {
                    $value = '';
                }

                if ($value !== '') {
                    $hasData = true;
                }

                $cleanRow[] = $value;
            }

            if ($hasData) {
                $rows[] = $cleanRow;
            }
        }

        return [
            "title" => $cleanHeaders[0] ?: "Tabla",
            "columns" => $cleanHeaders,
            "rows" => $rows
        ];

    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage()
        ];
    }
}


}