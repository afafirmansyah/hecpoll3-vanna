<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelRatio;
use Illuminate\Http\JsonResponse;

class FuelRatioController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'OB_BISM' => 'required|numeric',
            'COAL_BISM' => 'required|numeric',
            'PORT_BISM' => 'required|numeric',
        ]);

        try {
            FuelRatio::create([
                'date' => $request->date,
                'OB_BISM' => $request->OB_BISM,
                'COAL_BISM' => $request->COAL_BISM,
                'PORT_BISM' => $request->PORT_BISM,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Fuel ratio data saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save fuel ratio data: ' . $e->getMessage()
            ], 500);
        }
    }
}