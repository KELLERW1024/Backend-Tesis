<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;

class PowerPointService
{
    public function extract($file): ?string
    {
        try {

            $zip = new ZipArchive();

            if ($zip->open($file->getRealPath()) !== true) {
                return null;
            }

            $text = '';

            for ($i = 0; $i < $zip->numFiles; $i++) {

                $name = $zip->getNameIndex($i);

                // Solo leer las diapositivas
                if (!preg_match('/ppt\/slides\/slide\d+\.xml$/', $name)) {
                    continue;
                }

                $xml = $zip->getFromIndex($i);

                if (!$xml) {
                    continue;
                }

                $dom = new DOMDocument();
                @$dom->loadXML($xml);

                // Extraer todos los nodos <a:t>
                $nodes = $dom->getElementsByTagName('t');

                foreach ($nodes as $node) {
                    $text .= trim($node->nodeValue) . "\n";
                }

                $text .= "\n";
            }

            $zip->close();

            return trim($text);

        } catch (\Throwable $e) {
            return null;
        }
    }
}