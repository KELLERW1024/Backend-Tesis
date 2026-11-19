<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\ReferenceService;
use App\Services\SourceSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SourceController extends Controller
{
    public function __construct(
        protected SourceSearchService $sourceSearchService,
        protected ReferenceService $referenceService,
    ) {}

    public function search(Request $request)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'query' => 'required|string|min:3|max:300',
            'scope' => 'required|in:official,academic',
        ]);

        if (!$this->ownedConversation((int) $validated['idConversation'])) {
            return response()->json(['success' => false, 'message' => 'Conversación no encontrada.'], 404);
        }

        // Las búsquedas oficiales tienen costo: límite diario por usuario
        if ($validated['scope'] === 'official') {
            $key = 'sources_search:' . auth()->id() . ':' . now()->toDateString();
            $limit = (int) config('services.sources.daily_limit', 20);

            if ((int) Cache::get($key, 0) >= $limit) {
                return response()->json([
                    'success' => false,
                    'message' => "Llegaste al límite de {$limit} búsquedas en fuentes oficiales por hoy. Puedes agregar la fuente a mano.",
                ], 429);
            }

            Cache::put($key, (int) Cache::get($key, 0) + 1, now()->endOfDay());
        }

        try {
            $results = $validated['scope'] === 'official'
                ? $this->sourceSearchService->official(trim($validated['query']))
                : $this->sourceSearchService->academic(trim($validated['query']));
        } catch (\Throwable $e) {
            \Log::error('Error al buscar fuentes', ['scope' => $validated['scope'], 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar la búsqueda. Inténtalo de nuevo en unos segundos.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    // Fuente ingresada a mano: se valida y se devuelve con su texto APA 7
    public function format(Request $request)
    {
        $validated = $request->validate([
            'authors' => 'required|array|min:1',
            'authors.*' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:10',
            'title' => 'required|string|max:500',
            'source_type' => 'required|in:book,journal_article,web_page,thesis,report,conference_paper',
            'container_title' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:50',
            'issue' => 'nullable|string|max:50',
            'pages' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:1000',
            'doi' => 'nullable|string|max:100',
            'excerpt' => 'nullable|string|max:2000',
        ]);

        $reference = $this->referenceService->normalize($validated);

        if (!$reference) {
            return response()->json([
                'success' => false,
                'message' => 'Completa el autor o la institución y el título de la fuente.',
            ], 422);
        }

        $reference['excerpt'] = trim($validated['excerpt'] ?? '');
        $reference['apa_text'] = $this->referenceService->formatPlain($reference);

        return response()->json([
            'success' => true,
            'data' => $reference,
        ]);
    }

    // La conversación, solo si pertenece al usuario de la sesión
    private function ownedConversation(int $conversationId): ?Conversation
    {
        return Conversation::where('id', $conversationId)
            ->whereHas('userPlan', fn ($query) => $query->where('user_id', auth()->id()))
            ->first();
    }
}
