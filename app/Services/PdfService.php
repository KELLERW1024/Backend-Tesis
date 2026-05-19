<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;

class PdfService
{
    public function extract($file): ?string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());

            return $pdf->getText();

        } catch (\Exception $e) {
            return null;
        }
    }
}