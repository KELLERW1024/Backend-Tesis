<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;

class AttachmentTextService
{
    public function __construct(
        private FileTextExtractorService $fileProcessor,
        private OpenAIService $openAIService,
    ) {}

    public function extract(array $files): array
    {
        $documentContent = '';
        $imageFile = null;

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            // Solo se lee la primera imagen
            if (str_starts_with((string) $file->getMimeType(), 'image')) {
                $imageFile ??= $file;
                continue;
            }

            try {
                $extracted = $this->fileProcessor->extract($file);
            } catch (\Throwable $e) {
                \Log::error('Error lectura documento: ' . $e->getMessage());
                $extracted = null;
            }

            // Sin texto no se puede validar: no se acepta el documento
            if (trim((string) $extracted) === '') {
                return $this->failure('No se pudo leer el documento. Revisa que tenga texto legible.');
            }

            $documentContent .= $extracted . "\n\n";
        }

        $imageContent = '';

        if ($imageFile) {
            $imageDataUri = 'data:' . $imageFile->getMimeType() . ';base64,' . base64_encode(file_get_contents($imageFile->getRealPath()));

            try {
                $imageContent = $this->openAIService->imageInputStringOutput(
                    prompt: 'Debes obtener la informacion exacta de esta imagen sin comentarios extras. ',
                    image: $imageDataUri
                );
            } catch (ConnectionException $e) {
                \Log::error('OpenAI error lectura: ' . $e->getMessage());

                return $this->failure('No se pudo conectar. Inténtalo de nuevo en unos segundos.', 503);
            } catch (\Throwable $e) {
                \Log::error('OpenAI error lectura: ' . $e->getMessage());
                $imageContent = '';
            }

            // Sin texto no se puede validar: no se acepta la imagen
            if (trim($imageContent) === '') {
                return $this->failure('No se pudo leer la imagen. Inténtalo de nuevo.');
            }
        }

        if ($documentContent !== '') {
            $documentContent = $this->openAIService->inputCadenaOutputString($documentContent);
        }

        return ['document' => $documentContent, 'image' => $imageContent, 'error' => null, 'status' => 200];
    }

    private function failure(string $message, int $status = 422): array
    {
        return ['document' => '', 'image' => '', 'error' => $message, 'status' => $status];
    }
}
