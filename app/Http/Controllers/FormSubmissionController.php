<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\AmoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FormSubmissionController extends Controller
{
   protected AmoService $amoService;

   public function __construct(AmoService $amoService)
   {
      $this->amoService = $amoService;
   }

   /**
    * Обработать отправку формы обратной связи
    */
   public function submitContactForm(ContactFormRequest $request): JsonResponse
   {
      try {
         $validated = $request->validated();

         // Отправляем лид в amoCRM
         $leadId = $this->amoService->sendLead(
            name: $validated['name'],
            phone: $validated['phone'],
            email: $validated['email'] ?? null,
            brand: $validated['brand'] ?? null,
            model: $validated['model'] ?? null,
            comment: $validated['message'] ?? $validated['comment'] ?? null,
            source: 'Форма контактов'
         );

         Log::info('Форма контактов успешно отправлена в amoCRM', [
            'lead_id' => $leadId,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'timestamp' => now(),
         ]);

         return response()->json([
            'status' => 'success',
            'message' => 'Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в течение дня.',
            'lead_id' => $leadId,
         ], 201);
      } catch (\Exception $e) {
         Log::error('Ошибка при отправке формы контактов в amoCRM', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => $request->validated(),
            'timestamp' => now(),
         ]);

         return response()->json([
            'status' => 'error',
            'message' => 'Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.',
         ], 500);
      }
   }

   /**
    * Обработать отправку формы из каталога
    */
   public function submitCatalogForm(ContactFormRequest $request): JsonResponse
   {
      try {
         $validated = $request->validated();

         // Отправляем лид в amoCRM
         $leadId = $this->amoService->sendLead(
            name: $validated['name'],
            phone: $validated['phone'],
            email: $validated['email'] ?? null,
            brand: $validated['brand'] ?? null,
            model: $validated['model'] ?? null,
            comment: 'Запрос из каталога',
            source: 'Каталог'
         );

         Log::info('Форма из каталога успешно отправлена в amoCRM', [
            'lead_id' => $leadId,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'brand' => $validated['brand'] ?? null,
            'timestamp' => now(),
         ]);

         return response()->json([
            'status' => 'success',
            'message' => 'Спасибо! Ваш запрос отправлен. Мы свяжемся с вами в течение дня.',
            'lead_id' => $leadId,
         ], 201);
      } catch (\Exception $e) {
         Log::error('Ошибка при отправке формы из каталога в amoCRM', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => $request->validated(),
            'timestamp' => now(),
         ]);

         return response()->json([
            'status' => 'error',
            'message' => 'Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже.',
         ], 500);
      }
   }
}
