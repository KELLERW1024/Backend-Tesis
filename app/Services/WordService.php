<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;

class WordService
{
    public function extract($file): ?string
    {
        try {
            $phpWord = IOFactory::load($file->getRealPath());

            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {

                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            return $text;

        } catch (\Exception $e) {
            return null;
        }
    }
}