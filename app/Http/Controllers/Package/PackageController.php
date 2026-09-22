<?php

namespace App\Http\Controllers\Package;

use App\Http\Controllers\Controller;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query();

        if ($request->boolean('active_only')) {
            $query->where('is_active', 1);
            // ÚNICAMENTE los planes activos
            $query->with(['plans' => function ($q) {
                $q->where('is_active', 1);
            }]);
        } else {
            // Para el panel administrativo traemos todos los planes vinculados
            $query->with('plans');
        }
        $packages = Package::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ], 200);
    }

    public function getPackagePlans( Request $request )
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $idPackage = $request->idPackage;

        $package = Package::with(['plans' => function ($q) {
            $q->where('is_active', 1);
        }])->findOrFail($idPackage);

        return response()->json([
            'success' => true,
            'data' => $package
        ]);
    }

    public function store(StorePackageRequest $request)
    {
        $data = $request->validated();

        $plans = $data['plans'] ?? [];
        unset($data['plans']);

        $data['is_active'] = $request->input('is_active', 1);

        if (isset($data['benefits']) && is_array($data['benefits'])) {
            $data['benefits'] = json_encode($data['benefits']);
        }

        $package = DB::transaction(function () use ($data, $plans) {
            $createdPackage = Package::create($data);

            if (!empty($plans)) {
                $createdPackage->plans()->sync($plans);
            }

            return $createdPackage->load('plans');
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Paquete creado correctamente',
            'data'    => $package
        ], 201);
    }

    public function update(UpdatePackageRequest $request, $id)
    {
        $package = Package::findOrFail($id);

        $data = array_filter($request->validated(), fn($value) => !is_null($value));

        $hasPlans = array_key_exists('plans', $data);
        $plans = $data['plans'] ?? [];
        unset($data['plans']);

        if (isset($data['benefits']) && is_array($data['benefits'])) {
            $data['benefits'] = json_encode($data['benefits']);
        }

        DB::transaction(function () use ($package, $data, $hasPlans, $plans) {
            $package->update($data);

            if ($hasPlans) {
                $package->plans()->sync($plans);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Paquete actualizado correctamente',
            'data'    => $package->load('plans')
        ], 200);
    }

    public function syncPackagePlans(Request $request, $id)
    {
        $request->validate([
            'plans'   => 'present|array',
            'plans.*' => [
                    'integer',
                    Rule::exists('plans', 'id')->where(function ($query) {
                        $query->where('is_active', 1);
                    })
            ]
        ]);

        $package = Package::findOrFail($id);
        $package->plans()->sync($request->plans);

        return response()->json([
            'success' => true,
            'message' => 'Planes actualizados correctamente para el paquete',
            'data'    => $package->load('plans')
        ]);
    }
}
