<?php

namespace App\Http\Controllers\Package;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\Package;


class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', 1)
                // ->with('plans')
                ->get();

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    public function getPackagePlans( Request $request )
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $idPackage = $request->idPackage;

        $package = Package::with('plans')->findOrFail( $idPackage );

        return response()->json([
            'success' => true,
            'data' => $package
        ]);
    }
}
