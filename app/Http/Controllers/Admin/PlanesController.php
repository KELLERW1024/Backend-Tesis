<?php

namespace App\Http\Controllers\Admin;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanesController extends Controller
{
      public function index()
    {
        // Obtener todos los planes
         $plans = Plan::query()
            ->select([
                'id',
                'name',
                'code',
                'description',
                'price',
                'duration_days',
                'max_sections',
                'max_messages',
                'max_exports',
                'is_active'
            ])
            ////->where('is_active', true)
            //->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    public function obtenerCapitulosPlan( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $planId = $request->get('idPlan');
        

        $plan = Plan::with([
            'sections' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('order_index');
                },
            'sections.questions'
        ])->findOrFail($planId);

        // Adjuntar progress manualmente
        $plan->sections->map(function ($section) use ($userId) {

            $section->progress = \App\Models\ConversationSectionProgress::where('user_id', $userId)
                ->where('section_id', $section->id)
                ->first();

            return $section;
        });
        

        return new PlanResource($plan);

    }

    public function getPlanId(Request $request)
    {
        $idPlan = $request->get('idPlan');

        $plan = Plan::where('id', $idPlan)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json($plan);
    }

    public function show($id)
    {
        $plan = Plan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $plan
        ]);
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->input('is_active', 1);

        $plan = Plan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Plan creado correctamente',
            'data'    => $plan
        ], 201);
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $data = array_filter($request->validated(), fn($value) => !is_null($value));

        $plan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Plan actualizado correctamente',
            'data'    => $plan
        ]);
    }

    public function toggleStatus($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->is_active = $plan->is_active ? 0 : 1;
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado del plan actualizado',
            'data'    => $plan
        ]);
    }

    
}
