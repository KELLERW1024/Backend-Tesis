<?php

namespace App\Http\Controllers\Conversation;

use App\Http\Controllers\Controller;
use App\Services\StructureService;
use Illuminate\Http\Request;

class StructureController extends Controller
{
     public function __construct(
        private StructureService $service
    ) {}

    public function index(int $planId)
    {
        return response()->json(
            $this->service->getByPlan($planId)
        );
    }

    public function show(int $id)
    {
        return response()->json(
            $this->service->find($id)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id'      => ['required', 'integer'],
            'user_plan_id' => ['nullable', 'integer'],
            'parent_id'    => ['nullable', 'integer'],
            'titulo'       => ['required', 'string', 'max:200'],
            'orden'        => ['nullable', 'integer', 'min:1'],
            'objective'    => ['nullable', 'string'],
        ]);

        $node = $this->service->create($validated);

        return response()->json($node, 201);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'titulo'    => ['sometimes', 'required', 'string', 'max:200'],
            'objective' => ['sometimes', 'nullable', 'string'],
        ]);

        $node = $this->service->update($id, $validated);

        return response()->json($node);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' => 'Nodo eliminado correctamente'
        ]);
    }

    public function position(Request $request, int $id)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'orden'     => ['required', 'integer', 'min:1'],
        ]);

        $node = $this->service->changePosition(
            $id,
            $validated['parent_id'],
            $validated['orden']
        );

        return response()->json($node);
    }
}
