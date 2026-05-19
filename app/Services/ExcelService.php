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
}