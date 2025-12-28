<?php

namespace App\Http\Controllers;

use App\Services\AmoService;
use AmoCRM\Models\LeadModel;

class AmoTestController extends Controller
{
    public function test(AmoService $amo)
    {
        try {
            $lead = new LeadModel();
            $lead->setName('Тестовый лид из Laravel ' . now()->format('Y-m-d H:i:s'));

            $response = $amo->api()->leads()->addOne($lead);

            return response()->json([
                'status' => 'success',
                'message' => 'Лид успешно создан в amoCRM',
                'lead_id' => $response->getId(),
                'timestamp' => now(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => now(),
            ], 500);
        }
    }
}
