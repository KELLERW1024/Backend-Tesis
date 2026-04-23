<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use Illuminate\Http\Request;
use App\Models\Plans;

class PlanesController extends Controller
{
      public function index()
    {
        // Obtener todos los planes
        $plans = Plans::all();

        return response()->json($plans);
    }

    public function obtenerCapitulosPlan( Request $request ){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userId = auth()->id();
        $planId = $request->get('idPlan');
        

        $plan = Plans::with([
            'sections.questions'
        ])->findOrFail($planId);

        // Adjuntar progress manualmente
        $plan->sections->map(function ($section) use ($userId) {

            $section->progress = \App\Models\ConversationSectionProgress::where('user_id', $userId)
                ->where('section_id', $section->id)
                ->first();

            return $section;
        });
        //$plan = Plans::with('sections.questions')
          //  ->findOrFail($planId);

        return new PlanResource($plan);

    }
}
