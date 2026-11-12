<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Annex;
use App\Models\Conversation;
use App\Services\ConversationService;
use App\Services\ExcelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnexController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService,
        protected ExcelService $excelService,
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
        ]);

        $conversation = $this->ownedConversation((int) $validated['idConversation']);

        if (!$conversation) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'plan_completed' => $this->conversationService->isPlanCompleted($conversation),
                'annexes' => Annex::where('conversation_id', $conversation->id)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idConversation' => 'required|integer',
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,xls,xlsx|max:20480',
        ]);

        $conversation = $this->ownedConversation((int) $validated['idConversation']);

        if (!$conversation) {
            return $this->notFound();
        }

        if (!$this->conversationService->isPlanCompleted($conversation)) {
            return response()->json([
                'success' => false,
                'message' => 'Los anexos se pueden agregar cuando el plan está completo.',
            ], 422);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        // Del Excel solo se usa la primera hoja
        if (in_array($extension, ['xls', 'xlsx']) && $this->excelService->sheetsCount($file->getRealPath()) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo tiene varias hojas. Deja solo la que contiene tu información y vuelve a intentarlo.',
            ], 422);
        }

        // El anexo nuevo va al final
        $lastOrder = (int) Annex::where('conversation_id', $conversation->id)->max('sort_order');

        $annex = Annex::create([
            'conversation_id' => $conversation->id,
            'title' => trim($validated['title']),
            'source' => isset($validated['source']) ? trim($validated['source']) : null,
            'file_path' => $file->store("annexes/{$conversation->id}", 'public'),
            'sort_order' => $lastOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'data' => $annex,
        ], 201);
    }

    // Sube o baja el anexo una posición: intercambia su orden con el del anexo vecino
    public function move(Request $request, int $id)
    {
        $validated = $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $annex = Annex::find($id);

        if (!$annex || !$this->ownedConversation($annex->conversation_id)) {
            return $this->notFound();
        }

        DB::transaction(function () use ($annex, $validated) {
            $neighbor = Annex::where('conversation_id', $annex->conversation_id)
                ->where('id', '!=', $annex->id)
                ->when(
                    $validated['direction'] === 'up',
                    fn ($query) => $query->where('sort_order', '<=', $annex->sort_order)
                        ->orderByDesc('sort_order')->orderByDesc('id'),
                    fn ($query) => $query->where('sort_order', '>=', $annex->sort_order)
                        ->orderBy('sort_order')->orderBy('id')
                )
                ->lockForUpdate()
                ->first();

            if (!$neighbor) {
                return;
            }

            $currentOrder = $annex->sort_order;
            $neighborOrder = $neighbor->sort_order;

            if ($currentOrder === $neighborOrder) {
                $validated['direction'] === 'up' ? $currentOrder++ : $neighborOrder++;
            }

            $annex->update(['sort_order' => $neighborOrder]);
            $neighbor->update(['sort_order' => $currentOrder]);
        });

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(int $id)
    {
        $annex = Annex::find($id);

        if (!$annex || !$this->ownedConversation($annex->conversation_id)) {
            return $this->notFound();
        }

        Storage::disk('public')->delete($annex->file_path);
        $annex->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    // La conversación, solo si pertenece al usuario de la sesión
    private function ownedConversation(int $conversationId): ?Conversation
    {
        $userId = auth()->id();

        return Conversation::where('id', $conversationId)
            ->whereHas('userPlan', fn ($query) => $query->where('user_id', $userId))
            ->first();
    }

    private function notFound()
    {
        return response()->json([
            'success' => false,
            'message' => 'Anexo o conversación no encontrados.',
        ], 404);
    }
}
