<?php

namespace App\Services;

use App\Services\ExcelService;
use App\Services\PdfService;
use App\Services\WordService;

class FileTextExtractorService
{
      public function __construct(
        protected PdfService $pdf,
        protected WordService $word,
        protected ExcelService $excel,
        protected PowerPointService $powerPoint,
    ) {}
    public function extract($file): ?string
    {
       
        if (!$file) {
            return null;
        }

         $mime = $file->getMimeType();

        return match (true) {

            str_contains($mime, 'pdf') =>
                $this->pdf->extract($file),

            str_contains($mime, 'wordprocessingml') ||
            str_contains($mime, 'msword') =>
                $this->word->extract($file),

            str_contains($mime, 'spreadsheet') ||
            str_contains($mime, 'excel') =>
                $this->excel->extract($file),

            str_contains($mime, 'presentationml') ||
            str_contains($mime, 'powerpoint') =>
            $this->powerPoint->extract($file),

            default => null
        };
    }
     public function extractTable($file): ?string
    {
       
        if (!$file) {
            return null;
        }

         $mime = $file->getMimeType();

        return match (true) {

         

            str_contains($mime, 'spreadsheet') ||
            str_contains($mime, 'excel') =>
                $this->excel->extractTable($file),

            default => null
        };
    }
}