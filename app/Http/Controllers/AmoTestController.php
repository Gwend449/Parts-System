<?php

namespace App\Http\Controllers;

use App\Services\AmoService;
use AmoCRM\Models\LeadModel;

class AmoTestController extends Controller
{
    public function test(AmoService $amo)
    {
        $lead = new LeadModel();
        $lead->setName('Тестовый лид с Laravel');

        $amo->api()->leads()->addOne($lead);

        return 'Лид успешно отправлен в amoCRM';
    }
}
